<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaTools class.
 *
 * Many functions taken from sfAssetsLibraryTools class of sfAssetsLibraryPlugin
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  tools
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaTools
{

  public static function createAssetFolder($folder_path)
  {
    self::createFolder(self::getBasePath() . $folder_path);
  }
  public static function createFolder($folder_path)
  {
    //TODO: more error checking needed
    if(!file_exists($folder_path))
    {
      $old = umask(0);
      mkdir($folder_path, octdec(sfConfig::get('app_lyMediaManager_chmod_folder', '0770')));
      umask($old);
    }
  }
  public static function deleteAssetFiles($path, $filename, $thumbs = false)
  {
    $file = self::getBasePath() . $path . $filename;

    if(file_exists($file))
    {
      unlink($file);
    }

    if($thumbs)
    {
      self::deleteThumbnails($path, $filename);
    }
  }
  
  public static function deleteAssetFolder($folder_path)
  {
    
    $path = self::getBasePath() . $folder_path;
    $paths = array($path, $path . self::getThumbnailFolder());

    $fs = new sfFilesystem();
    $fs->remove(array_filter($paths, 'file_exists'));
  }

  public static function deleteThumbnails($path, $filename)
  {
    $path = self::getBasePath() . $path . self::getThumbnailFolder() . DIRECTORY_SEPARATOR;

    foreach(self::getThumbnailSettings() as $key => $params)
    {
      $file = $path . $key . '_' . $filename;
      if(file_exists($file))
      {
        unlink($file);
      }
    }
  }

  public static function formatAssetCaption($asset)
  {
    return(nl2br(wordwrap($asset->getFilename(),sfConfig::get('app_lyMediaManager_caption_row_max_chars',20), "\n", true)));
  }

  public static function generateThumbnails($folder, $filename)
  {
    $source = self::getBasePath() . $folder . $filename;
    $thumbnailSettings = self::getThumbnailSettings();

    foreach ($thumbnailSettings as $key => $params)
    {
      $width  = $params['width'];
      $height = $params['height'];
      $shave  = isset($params['shave']) ? $params['shave'] : false;
      self::generateThumbnail($source, self::getThumbnailPath($folder, $filename, $key), $width, $height, $shave);
    }
  }

  public static function generateThumbnail($source, $dest, $width, $height, $shave_all = false)
  {
    if (class_exists('sfThumbnail') && file_exists($source))
    {
      if (sfConfig::get('app_lyMediaManager_use_ImageMagick', false))
      {
        $adapter = 'sfImageMagickAdapter';
        $mime = 'image/jpg';
      }
      else
      {
        $adapter = 'sfGDAdapter';
        $mime = 'image/jpeg';
      }
      if ($shave_all)
      {
        $thumbnail  = new sfThumbnail($width, $height, false, true, 85, $adapter, array('method' => 'shave_all'));
        $thumbnail->loadFile($source);
        $thumbnail->save($dest, $mime);
        return true;
      }
      else
      {
        list($w, $h, $type, $attr) = getimagesize($source);
        $newHeight = $width ? ceil(($width * $h) / $w) : $height;
        $thumbnail = new sfThumbnail($width, $newHeight, true, true, 85, $adapter);
        $thumbnail->loadFile($source);
        $thumbnail->save($dest, $mime);
        return true;
      }
    }
    return false;
  }

  public static function getAllowedExtensions()
  {
    return sfConfig::get('app_lyMediaManager_allowed_extensions',
      array('jpg','png','gif'));
  }

  public static function getAllowedMimeTypes()
  {
    return sfConfig::get('app_lyMediaManager_mime_types',
      array(
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'image/gif'
      ));
  }

  public static function getAssetURI($asset)
  {
    return '/' . $asset->getPath();
  }

  public static function getBasePath()
  {
    return sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR;
  }

  public static function getThumbnailFile($asset, $type = 'small')
  {
    if($asset->supportsThumbnails())
    {
      $thumbnail = $type . '_' . $asset->getFilename();
    }
    else
    {
      switch($asset->getType())
      {
        case 'text/plain':
          $thumbnail = 'txt.png';
          break;
        default:
          $thumbnail = 'unknown.png';
          break;
      }
    }
    return $thumbnail;
  }

  public static function getThumbnailFolder()
  {
    return trim(sfConfig::get('app_lyMediaManager_thumbnail_folder', 'thumbs'), "\/");
  }

  public static function getThumbnailPath($path, $filename, $thumbnailType, $create = true)
  {
    $folder = self::getBasePath() . $path . self::getThumbnailFolder();
    
    if($create && !file_exists($folder))
    {
      self::createFolder($folder);
    }
    return $folder . DIRECTORY_SEPARATOR . $thumbnailType . '_' . $filename;
  }

  public static function getThumbnailSettings()
  {
    return sfConfig::get('app_lyMediaManager_thumbnails', array(
      'small' => array('width' => 84, 'height' => 84, 'shave' => true),
      'medium' => array('width' => 194, 'height' => 152)
    ));
  }

  public static function getThumbnailURI($asset, $folder_path, $type = 'small')
  {
    if($asset->supportsThumbnails())
    {
      $img = '/' . (isset($folder_path) ? $folder_path : $asset->getFolderPath()) . self::getThumbnailFolder() . '/' . self::getThumbnailFile($asset, $type);
    }
    else
    {
      $img = '/lyMediaManagerPlugin/images/' . self::getThumbnailFile($asset, $type);
    }

    return $img;
  }
  
  public static function moveAssetFiles($old_path, $old_fname, $new_path, $new_fname, $thumbs = false)
  {
    $src = self::getBasePath() . $old_path . (isset($old_fname) ? $old_fname : $new_fname);
    $dest = self::getBasePath() . $new_path . $new_fname;

    if(!file_exists($dest))
    {
      rename($src, $dest);
      
      if($thumbs)
      {
        self::moveThumbnails($old_path, $old_fname, $new_path, $new_fname);
      }
    }
  }

  public static function moveAssetFolder($old_path, $new_path)
  {
    $old = self::getBasePath() . $old_path;
    $new = self::getBasePath() . $new_path;

    if(is_dir($old) && !file_exists($new))
    {
      rename($old, $new);
    }
  }
  
  public static function moveThumbnails($old_path, $old_fname, $new_path, $new_fname)
  {
    $src_path = self::getBasePath() . $old_path . self::getThumbnailFolder() . DIRECTORY_SEPARATOR;
    $dest_path = self::getBasePath() . $new_path . self::getThumbnailFolder() . DIRECTORY_SEPARATOR;
    $src_file = isset($old_fname) ? $old_fname : $new_fname;
    
    if(!file_exists($dest_path))
    {
      //Create thumbnail folder
       self::createFolder($dest_path);
    }
    foreach(self::getThumbnailSettings() as $key => $params)
    {
      $src = $src_path . $key . '_' . $src_file;
      $dest = $dest_path . $key . '_' . $new_fname;
      
      if(file_exists($src) && !file_exists($dest))
      {
        rename($src, $dest);
      }
    }
  }
}
