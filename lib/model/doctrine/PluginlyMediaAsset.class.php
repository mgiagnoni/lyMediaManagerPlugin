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
  public function generateFilenameFilename(sfValidatedFile $file)
  {
    $ofile = pathinfo($file->getOriginalName(), PATHINFO_FILENAME);
    $filename = $ofile . $file->getOriginalExtension();

    for($i = 1; $i <= 999; $i++)
    {
      if(!file_exists($file->getPath() . DIRECTORY_SEPARATOR . $filename))
      {
        break;
      }
      $filename = $ofile . '(' . $i . ')' . $file->getOriginalExtension();
    }
    return $filename;
  }

  public function getPath()
  {
    return $this->getFolderPath() . $this->getFilename();
  }

  public function getFolderPath()
  {
    return $this->getFolder()->getRelativePath();
  }
  
  public function postDelete($event)
  {
    $record = $event->getInvoker();

    lyMediaTools::deleteAssetFiles($record->getFolderPath(), $record->getFilename(), $record->supportsThumbnails());
  }

  public function preSave($event)
  {
    $record = $event->getInvoker();
    $modified = $record->getModified(true);

    if($record->isNew() && $record->supportsThumbnails())
    {
      lyMediaTools::generateThumbnails($record->getFolderPath(), $record->getFilename());
    }
    else
    {
      if(isset($modified['folder_id']) || isset($modified['filename']))
      {
        //Selected new folder or edited filename: move/rename asset

        //Relation still not saved: we need find method to get modified folder
        $new_folder = Doctrine::getTable('lyMediaFolder')
          ->find($record->getFolderId());

        $old_fname = (isset($modified['filename'])) ? $modified['filename'] : null;
        lyMediaTools::moveAssetFiles($record->getFolder()->getRelativePath(), $old_fname, $new_folder->getRelativePath(), $record->getFileName(), $record->supportsThumbnails());
      }
    }
  }

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