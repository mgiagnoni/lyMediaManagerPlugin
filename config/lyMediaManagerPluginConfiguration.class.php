<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaManagerPlugin configuration.
 * 
 * @package     lyMediaManagerPlugin
 * @subpackage  config
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaManagerPluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '1.0.0-DEV';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {

    if (in_array('lyMediaAsset', sfConfig::get('sf_enabled_modules', array())))
    {
      $this->dispatcher->connect('routing.load_configuration', array('lyMediaManagerRouting', 'addRouteForAdmin'));
    }
  }
}
