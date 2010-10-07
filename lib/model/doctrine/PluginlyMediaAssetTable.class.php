<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaAsset table class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  model
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class PluginlyMediaAssetTable extends Doctrine_Table
{
  /**
   * Retrieves a list of assets.
   * table_method in list configuration of lyMediaAsset module
   *
   * @param Doctrine_Query $q
   */
  public function retrieveAssetList(Doctrine_Query $q)
  {
    $q->innerJoin($q->getRootAlias() . '.Folder f');

    return $q;

  }


  /**
   * Whether or not this class implements I18n
   *
   * @return boolean
   */
  public function isI18n()
  {
    return $this->hasTemplate('Doctrine_Template_I18n');
  }
}
