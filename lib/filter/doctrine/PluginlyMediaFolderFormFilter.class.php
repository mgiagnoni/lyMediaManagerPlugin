<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaFolder filter class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  filter
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
abstract class PluginlyMediaFolderFormFilter extends BaselyMediaFolderFormFilter
{
  public function setup()
  {
    parent::setup();
    unset($this['lft'],$this['rgt'],$this['level'],$this['relative_path']);
  }
}
