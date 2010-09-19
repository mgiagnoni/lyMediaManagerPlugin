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

  public static function log($message, $color = '')
  {
    switch ($color)
    {
      case 'green':
        $message = "\033[32m".$message."\033[0m\n";
        break;
      case 'red':
        $message = "\033[31m".$message."\033[0m\n";
        break;
      case 'yellow':
        $message = "\033[33m".$message."\033[0m\n";
        break;
      default:
        $message = $message . "\n";
    }
    fwrite(STDOUT, $message);
  }
  public static function splitPath($path, $separator = DIRECTORY_SEPARATOR)
  {
    $path = rtrim($path, $separator);
    $dirs = preg_split('/' . preg_quote($separator, '/') . '+/', $path);
    $name = array_pop($dirs);
    $relativePath =  implode($separator, $dirs);

    return array($relativePath, $name);
  }
}