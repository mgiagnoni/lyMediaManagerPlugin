<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generator configuration class for the lyMediaManagerPlugin lyMediaAsset module.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  lyMediaAsset
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaAssetGeneratorConfiguration extends BaseLyMediaAssetGeneratorConfiguration
{
  public function getFormOptions()
  {
    return array(
      'upload_root' => sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR,
      'user' => sfContext::getInstance()->getUser(),
      'folder_id' => sfContext::getInstance()->getRequest()->getParameter('folder_id'),
    );
  }
}
