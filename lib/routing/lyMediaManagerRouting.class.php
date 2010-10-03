<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaManagerPlugin routing class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  routing
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaManagerRouting
{
  
  static public function addRouteForAdmin(sfEvent $event)
  {
    $r = $event->getSubject();

    $r->prependRoute('ly_media_asset', new sfDoctrineRouteCollection(array(
      'name' => 'ly_media_asset',
      'model' => 'lyMediaAsset',
      'module' => 'lyMediaAsset',
      'prefix_path' => 'ly_media_asset',
      'with_wildcard_routes' => true,
      'collection_actions' => array('filter' => 'post', 'batch' => 'post'),
      'requirements' => array(),
      'object_actions' => array('download' => 'get')
    )));

    $r->prependRoute('ly_media_folder', new sfDoctrineRouteCollection(array(
      'name' => 'ly_media_folder',
      'model' => 'lyMediaFolder',
      'module' => 'lyMediaFolder',
      'prefix_path' => 'ly_media_folder',
      'with_wildcard_routes' => true,
      'collection_actions' => array('filter' => 'post', 'batch' => 'post'),
      'requirements' => array(),
    )));
    
    $r->prependRoute('ly_media_asset_icons', new sfRoute('ly_media_asset/icons/:folder_id', array('module' => 'lyMediaAsset', 'action' => 'icons', 'folder_id' => 0)));
    $r->prependRoute('ly_media_asset_popup', new sfRoute('ly_media_asset/popup', array('module' => 'lyMediaAsset', 'action' => 'popup')));

  }
}
