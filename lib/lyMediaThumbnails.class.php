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
  protected $source;
  protected $thumb_file;
  protected $settings;
  protected $mime_type;

  /**
   * Constructor.
   *
   * @param string $source source file path (image file from which thumbnails will be generated).
   * @param string $mime thumbnail mime-type
   * @param string $thumb_file thumbnail filename (without type prefix)
   */
  public function __construct($source, $mime, $thumb_file)
  {
    $fs = new lyMediaFileSystem();

    $this->source = $fs->makePathAbsolute($source);
    $this->thumb_file = $thumb_file;
    $this->mime_type = $mime;
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
    $folder = pathinfo($this->source, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . self::getThumbnailFolder();
    if($create && !file_exists($folder))
    {
      $fs = new lyMediaFileSystem();
      $fs->mkdir($folder);
    }
    return $folder . DIRECTORY_SEPARATOR . $thumb_type . '_' . $this->thumb_file;
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
   * @param string $thumb_type thumbnail type
   * @param array $thumb_options thumbnail options (width, height and other as set in configuration).
   * @return bool true = success, false = failure.
   */
  protected function generateThumbnail($thumb_type, $thumb_options)
  {
    if (!class_exists('sfThumbnail') || !file_exists($this->source))
    {
      return false;
    }
    
    $width = isset($thumb_options['width']) ? $thumb_options['width'] : null;
    $height = isset($thumb_options['height']) ? $thumb_options['height'] : null;
    $options = array('extract' => 1);
    $scale = true;
    if (isset($thumb_options['shave']) && $thumb_options['shave'] == true)
    {
      $options = array_merge(array('method' => 'shave_all'), $options);
      $scale = false;
    }
    $thumbnail  = new sfThumbnail($width, $height,
      $scale, true, 85,
      sfConfig::get('app_lyMediaManager_use_ImageMagick', false) ? 'sfImageMagickAdapter' : 'sfGDAdapter',
      $options
    );
    $thumbnail->loadFile($this->source);
    $thumbnail->save($this->getThumbnailPath($thumb_type), $this->mime_type);
    return true;
  }
}