<?php

/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaThumbnails.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  thumbnail generator
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaThumbnails
{
  protected $folder;
  protected $file;
  protected $settings;

  /**
   * Constructor.
   *
   * @param string $src_folder source folder path
   * @param string $src_file source file name (image file from which thumbnails will be generated).
   */
  public function __construct($src_folder, $src_file)
  {
    $fs = new lyMediaFileSystem();

    $this->folder = $fs->makePathAbsolute($src_folder);
    $this->file = $src_file;
    $this->settings = self::getThumbnailSettings();
  }

  /**
   * Generates thumbnails of all types set in configuration..
   *
   */
  public function generate()
  {
    foreach ($this->settings as $key => $params)
    {
      $this->generateThumbnail($key, $params);
    }
  }

  /**
   * Gets thumbnail folder name as set in plugin configuration.
   *
   * @return string
   */
  public static function getThumbnailFolder()
  {
    return trim(sfConfig::get('app_lyMediaManager_thumbnail_folder', 'thumbs'), "\/");
  }

  /**
   * Gets full path of a thumbnail file of a given type.
   *
   * @param string $thumb_type thumbnail type.
   * @param bool $create true = create thumbnail folder if it doesn't exists.
   * @return string thumbnail file path.
   */
  public function getThumbnailPath($thumb_type, $create = true)
  {
    $folder = $this->folder . self::getThumbnailFolder();

    if($create && !file_exists($folder))
    {
      $fs = new lyMediaFileSystem();
      $fs->mkdir($folder);
    }
    return $folder . DIRECTORY_SEPARATOR . $thumb_type . '_' . $this->file;
  }

  /**
   * Returns an array with paths of all thumbnails of source file.
   *
   * @return array of paths
   */
  public function getThumbnailPaths()
  {
    $paths = array();
    foreach (array_keys($this->settings) as $key)
    {
      $paths[] = $this->getThumbnailPath($key, false);
    }
    return $paths;
  }

  /**
   * Gets thumbnail configuration parameters.
   *
   * @return array
   */
  public static function getThumbnailSettings()
  {
    return sfConfig::get('app_lyMediaManager_thumbnails', array(
      'small' => array('width' => 84, 'height' => 84, 'shave' => true),
      'medium' => array('width' => 194, 'height' => 152)
    ));
  }

  /**
   * Generate a thumbnail of a given type.
   * 
   * @param <type> $thumb_type thumbnail type
   * @param <type> $thumb_options thumbnail options (width, height and other as set in configuration).
   * @return bool true = success, false = failure.
   */
  protected function generateThumbnail($thumb_type, $thumb_options)
  {
    $source = $this->folder . $this->file;
    $dest = $this->getThumbnailPath($thumb_type);
    $width = $thumb_options['width'];
    $height = $thumb_options['height'];
    $shave_all = isset($thumb_options['shave']) ? $thumb_options['shave'] : false;

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
}