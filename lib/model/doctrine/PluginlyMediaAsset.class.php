<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaAsset record class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  model
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
abstract class PluginlyMediaAsset extends BaselyMediaAsset
{
  /**
   * Generates a file name for uploaded asset.
   *
   * @param sfValidatedFile $file
   * @return string unique file name.
   */
  public function generateFilenameFilename(sfValidatedFile $file)
  {
    $fs = new lyMediaFileSystem();
    return $fs->generateUniqueFilename($file->getPath(), $file->getOriginalName());
  }

  /**
   * Returns asset file path.
   *
   * @return string, path (relative to web dir).
   */
  public function getPath()
  {
    return $this->getFolderPath() . $this->getFilename();
  }

  /**
   * Returns asset folder path.
   *
   * @return string, folder path (relative to web dir).
   */
  public function getFolderPath()
  {
    return $this->getFolder()->getRelativePath();
  }

  /**
   * Returns asset thumbnail filename.
   *
   * @param string $thumb_type thumbnail type.
   */
  public function getThumbnailFile($thumb_type = 'small')
  {
    $thumbnail = 'unknown.png';

    if($this->supportsThumbnails())
    {
      $thumbnail = $thumb_type . '_' . $this->getFilename();
    }
    else
    {
      list($mtype, $mstype) = explode('/', $this->getType());

      switch($mtype)
      {
        case 'image':
          $thumbnail = 'image-x-generic.png';
          break;
        case 'application':
          switch($mstype)
          {
            case 'pdf':
            case 'x-pdf':
              $thumbnail = 'application-pdf.png';
              break;
          }
          break;
        case 'text':
          $thumbnail = 'text-x-generic.png';
          break;
        }
      }

    return $thumbnail;
  }

  /**
   * postDelete.
   *
   * @param Doctrine_Event $event
   */
  public function postDelete($event)
  {
    $record = $event->getInvoker();
    $fs = new lymediaFileSystem();
    $fs->unlink($record->getPath(), $record->supportsThumbnails());
  }

  /**
   * preSave.
   *
   * @param Doctrine_Event $event
   */
  public function preSave($event)
  {
    $record = $event->getInvoker();

    if($record->isNew())
    {
      $file = $record->getFilename();
      if($file != basename($file) && is_file($file))
      {
        $fs = new lyMediaFileSystem();
        $dest_path = $record->getFolderPath();
        $dest_file = $fs->generateUniqueFileName($dest_path, basename($file));
        $fs->import($file, $dest_path . $dest_file);
        $record->setType(mime_content_type($file));
        $record->setFilename($dest_file);
      }
      if($record->supportsThumbnails())
      {
        $tn = new lyMediaThumbnails($record->getFolderPath(), $record->getFilename());
        $tn->generate();
      }
    }
    else
    {
      $modified = $record->getModified(true);
      if(isset($modified['folder_id']) || isset($modified['filename']))
      {
        //Selected new folder or edited filename: move/rename asset
        $dest_folder = Doctrine::getTable('lyMediaFolder')
          ->find($record->getFolderId());

        $src_folder = $dest_folder;
        if(isset($modified['folder_id']))
        {
          $src_folder = Doctrine::getTable('lyMediaFolder')
          ->find($modified['folder_id']);
        }

        $src = $src_folder->getRelativePath() . (isset($modified['filename']) ? $modified['filename'] : $record->getFileName());
        $dest = $dest_folder->getRelativePath() . $record->getFileName();
        $fs = new lyMediaFileSystem();
        $fs->rename($src, $dest, $record->supportsThumbnails());
      }
    }
  }

  /**
   * Checks if asset supports thumbnails.
   *
   * @return bool, true if thumbnails are supported.
   */
  public function supportsThumbnails()
  {
    return in_array($this->getType(),
      sfConfig::get('app_lyMediaManager_create_thumbnails_for', array(
      'image/jpeg',
      'image/pjpeg',
      'image/png',
      'image/x-png',
      'image/gif'
      )));
  }
}