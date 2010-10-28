<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaAsset filter class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  filter
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
abstract class PluginlyMediaAssetFormFilter extends BaselyMediaAssetFormFilter
{
  public function setup()
  {
    parent::setup();
    $this->widgetSchema['folder_id']->setOption('method','getIndentName');
    $this->widgetSchema['folder_id']->setOption('order_by', array('lft', ''));
  }
}
