<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaTestFunctional.
 *
 * Shortcuts for common actions in functional tests.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  test
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaTestFunctional extends sfTestFunctional
{
  /**
   * Checks file existence in filesystem.
   *
   * @see checkFile()
   */
  public function isFile($asset)
  {
    return $this->checkFile($asset);
  }

  /**
   * Checks file non existence in filesystem.
   *
   * @see checkFile()
   */
  public function isntFile($asset)
  {
    return $this->checkFile($asset, false);
  }

  /**
   * Saves a form that creates a new record.
   *
   * @see saveForm()
   */
  public function saveNew($class, $data)
  {
    return $this->saveForm($class, $data, true);
  }

  /**
   * Saves a form that edits a record.
   *
   * @see saveForm()
   */
  public function saveEdit($class, $data)
  {
    return $this->saveForm($class, $data, false);
  }

  /**
   * Checks if asset file and asset thumbnails (if supported) exist in filesystem.
   *
   * @param lyMediaAsset $asset
   * @param boolean $exist true=must exist, false=must not exist
   * 
   * @return lyMediaTestFunctional current lyMediaTestFunctional instance
   */
  protected function checkFile($asset, $exist = true)
  {
    $fs = new lyMediaFileSystem();
    $file_path = $asset->getPath();
    $this->test()->is($fs->is_file($file_path), $exist, 'File ' . $file_path . ($exist ? ' has ' : ' has not ') . 'been found');

    if($asset->supportsThumbnails())
    {
      $tn = new lyMediaThumbnails(
        $file_path,
        in_array($asset->getType(), array('image/png','image/gif')) ? $asset->getType() : 'image/jpeg',
        $asset->getThumbnailFile(null)
      );
      foreach($tn->getThumbnailPaths() as $file_path)
      {
        $this->test()->is($fs->is_file($file_path), $exist, 'Thumbnail ' . basename($file_path) . ($exist ? ' has ' : ' has not ') . 'been found');
      }
    }
    return $this;
  }

  /**
   * Creates / edits a record by submitting an admin generator form
   *
   * @param string $class model/module (assumed same name)
   * @param array $data data sent to the form
   * @param boolean $new true=new, false=edit
   *
   * @return lyMediaTestFunctional current lyMediaTestFunctional instance
   */
  protected function saveForm($class, $data, $new = false)
  {
    return $this->click('li.sf_admin_action_save input', $data)->

    with('request')->begin()->
      isParameter('module', $class)->
      isParameter('action', $new ? 'create' : 'update')->
    end()->

    with('form')->
      hasErrors(false)->

    with('response')->
      isRedirected()->

    followRedirect()->
    with('request')->begin()->
      isParameter('module', $class)->
      isParameter('action', 'edit')->
    end()->

    with('response')->begin()->
      isStatusCode(200)->
      checkForm($class . 'Form')->
    end();
  }
}
