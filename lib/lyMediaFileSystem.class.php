<?php

/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaFileSystem.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  File system
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */

class lyMediaFileSystem
{
  protected $base;

  public function __construct()
  {
    $this->base = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR;
  }

  /**
   * Generates a unique filename in a given path.
   *
   * @param string $path folder path.
   * @param string $fname filename.
   *
   * @return string unique file name.
   */
  public function generateUniqueFilename($path, $fname)
  {
    $info = pathinfo($fname);
    $path = rtrim($this->makePathAbsolute($path), DIRECTORY_SEPARATOR);

    for($i = 1; $i <= 999; $i++)
    {
      if(!file_exists($path . DIRECTORY_SEPARATOR . $fname))
      {
        break;
      }
      $fname = $info['filename'] . '(' . $i . ').' . $info['extension'];
    }
    return $fname;
  }

  /**
   * Imports (copy) a file into the media library.
   *
   * @param string $src full path of the file to import.
   * @param string $dest destination path (can be relative to web dir).
   */
  public function import($src, $dest)
  {
    $dest = $this->makePathAbsolute($dest);
    copy($src, $dest);
  }

  /**
   * Transforms a path relative to web dir (for example the value of relativePath
   * property of lyMediaFolder class) in absolute path.
   *
   * @param string $path relative path.
   * @return string, absolute path.
   */
  public function makePathAbsolute($path)
  {
    if(strpos($path, $this->base) === 0)
    {
      //path is already absolute
      return $path;
    }
    return $this->base . $path;
  }

  /**
   * Creates a folder an sets permissions accordingly to plugin configuration.
   *
   * @param string $dir folder path (can be relative to web dir).
   */
  public function mkdir($dir)
  {
    $dir = $this->makePathAbsolute($dir);
    //TODO: more error checking needed
    if(!file_exists($dir))
    {
      $old = umask(0);
      mkdir($dir, octdec(sfConfig::get('app_lyMediaManager_chmod_folder', '0770')));
      umask($old);
    }
  }

  /**
   * Renames (or moves) a file and related thumbnails.
   *
   * @param string $src path of source file (can be relative to web dir).
   * @param string $dest detination path (can be relative to web dir).
   * @param bool $thumbs, if true thumbnails are also moved/renamed.
   */
  public function rename($src, $dest, $thumbs = false)
  {
    $src = $this->makePathAbsolute($src);
    $dest = $this->makePathAbsolute($dest);

    if(file_exists($dest))
    {
      //TODO: exception?
    }

    rename($src, $dest);
    if($thumbs)
    {
      $src_info = pathinfo($src);
      $dest_info = pathinfo($dest);
      $src_path = $src_info['dirname'] . DIRECTORY_SEPARATOR . lyMediaTools::getThumbnailFolder() . DIRECTORY_SEPARATOR;
      $dest_path = $dest_info['dirname'] . DIRECTORY_SEPARATOR . lyMediaTools::getThumbnailFolder() . DIRECTORY_SEPARATOR;

      if(!file_exists($dest_path))
      {
      //Create thumbnail folder
       $this->mkdir($dest_path);
      }
      foreach($this->getThumbnailTypes() as $key)
      {
        $src = $src_path . $key . '_' . $src_info['basename'];
        $dest = $dest_path . $key . '_' . $dest_info['basename'];

        if(file_exists($src) && !file_exists($dest))
        {
          rename($src, $dest);
        }
      }
    }
  }

  /**
   * Deletes a file and related thumbnails.
   *
   * @param string $file path of the file to delete (can be relative to web dir).
   * @param bool $thumbs, if true thubnail files are also deleted.
   */
  public function unlink($file, $thumbs = false)
  {
    $file = $this->makePathAbsolute($file);

    if(file_exists($file))
    {
      unlink($file);
    }

    if($thumbs)
    {
      $info = pathinfo($file);
      $path = $info['dirname'] . DIRECTORY_SEPARATOR . lyMediaTools::getThumbnailFolder() . DIRECTORY_SEPARATOR;

      foreach($this->getThumbnailTypes() as $key)
      {
        $file = $path . $key . '_' . $info['basename'];
        if(file_exists($file))
        {
          unlink($file);
        }
      }
    }
  }

  /**
   * Returns thumbnail types from plugin configuration.
   * 
   * @return array
   */
  protected function getThumbnailTypes()
  {
    return array_keys(
      sfConfig::get('app_lyMediaManager_thumbnails', array(
      'small' => array('width' => 84, 'height' => 84, 'shave' => true),
      'medium' => array('width' => 194, 'height' => 152)
      ))
    );
  }
}
