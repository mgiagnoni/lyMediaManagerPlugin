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
   * Checks if a path is a regular directory.
   *
   * @param string $path (can be relative to web dir)
   * @return bool
   */
  public function is_dir($path)
  {
    $path = $this->makePathAbsolute($path);
    return is_dir($path);
  }

  /**
   * Checks if a path is a regular file.
   *
   * @param string $path (can be relative to web dir)
   * @return bool
   */
  public function is_file($path)
  {
    $path = $this->makePathAbsolute($path);
    return is_file($path);
  }
  
  /**
   * Checks if a folder/file is writable.
   * 
   * @param string $path (can be relative to web dir)
   * @return bool
   */
  public function is_writable($path)
  {
    $path = $this->makePathAbsolute($path);
    return is_writable($path);
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
   * Creates a folder and sets permissions accordingly to plugin configuration.
   *
   * @param string $dir folder path (can be relative to web dir).
   */
  public function mkdir($dir)
  {
    $dir = $this->makePathAbsolute($dir);

    if(!file_exists($dir))
    {
      $old = umask(0);
      $ok = @mkdir($dir, octdec(sfConfig::get('app_lyMediaManager_chmod_folder', '0755')));
      umask($old);
      if(!$ok)
      {
        throw new lyMediaException('Can\'t create folder');
      }
    }
  }

  /**
   * Renames (or moves) a file and related thumbnails.
   *
   * @param string $src path of source file (can be relative to web dir).
   * @param string $dest destination path (can be relative to web dir).
   * @param string $src_thumb, source thumbnail file, null if thumbnails are not supported.
   * @param string $dest_thumb destination thumbnail file.
   */
  public function rename($src, $dest, $src_thumb = null, $dest_thumb = null)
  {
    $src = $this->makePathAbsolute($src);
    $dest = $this->makePathAbsolute($dest);

    if(file_exists($dest))
    {
      //TODO: exception?
    }

    rename($src, $dest);
    if($src_thumb)
    {
      $src_info = pathinfo($src);
      $dest_info = pathinfo($dest);
      $src_path = $src_info['dirname'] . DIRECTORY_SEPARATOR . lyMediaThumbnails::getThumbnailFolder() . DIRECTORY_SEPARATOR;
      $dest_path = $dest_info['dirname'] . DIRECTORY_SEPARATOR . lyMediaThumbnails::getThumbnailFolder() . DIRECTORY_SEPARATOR;

      if(!file_exists($dest_path))
      {
      //Create thumbnail folder
       $this->mkdir($dest_path);
      }
      foreach($this->getThumbnailTypes() as $key)
      {
        $src = $src_path . $key . '_' . $src_thumb;
        $dest = $dest_path . $key . '_' . $dest_thumb;

        if(file_exists($src) && !file_exists($dest))
        {
          rename($src, $dest);
        }
      }
    }
  }

  /**
   * Deletes a folder and thumbnail folder (if any).
   *
   * @param string $dir folder path (can be relative to web dir).
   * @param bool $rm_thumbs, if true removes thumbnail folder if present
   */
  public function rmdir($dir, $rm_thumbs = true)
  {
    $dirs[] = $this->makePathAbsolute($dir);
    if($rm_thumbs)
    {
      $dirs[] = rtrim($dirs[0], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . lyMediaThumbnails::getThumbnailFolder();
    }
    foreach(array_reverse(array_filter($dirs, 'is_dir')) as $dir)
    {

      if(!@rmdir($dir))
      {
        throw new lyMediaException('Can\'t delete folder');
      }
    }
  }
  
  /**
   * Deletes a file and related thumbnails.
   *
   * @param string $file path of the file to delete (can be relative to web dir).
   * @param string $thumb_file, thumbnail file, null if thumbnails are not supported.
   */
  public function unlink($file, $thumb_file = null)
  {
    $file = $this->makePathAbsolute($file);

    if($thumb_file)
    {
      $info = pathinfo($file);
      $path = $info['dirname'] . DIRECTORY_SEPARATOR . lyMediaThumbnails::getThumbnailFolder() . DIRECTORY_SEPARATOR;

      foreach($this->getThumbnailTypes() as $key)
      {
        $tfile = $path . $key . '_' . $thumb_file;

        if(file_exists($tfile))
        {
          if(!@unlink($tfile))
          {
            throw new lyMediaException('Can\'t delete thumbnail "%file%" (permission denied).', array('%file%' => basename($tfile)));
          }
        }
      }
    }
    if(file_exists($file))
    {
      if(!@unlink($file))
      {
        throw new lyMediaException('Can\'t delete file "%file%" (permission denied).', array('%file%' => basename($file)));
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
    return array_keys(lyMediaThumbnails::getThumbnailSettings());
  }
}
