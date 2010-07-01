<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base actions for the lyMediaManagerPlugin lyMediaAsset module.
 * 
 * @package     lyMediaManagerPlugin
 * @subpackage  lyMediaAsset
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
abstract class BaselyMediaAssetActions extends autoLyMediaAssetActions
{
  /**
   * Shows assets as list.
   *
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('view', 'list');
    parent::executeIndex($request);
  }
  /**
   * Shows assets as icons.
   *
   * @param sfWebRequest $request
   */
  public function executeIcons(sfWebRequest $request)
  {
    if($request->hasParameter('page'))
    {
      $this->getUser()->setAttribute('page', $request->getParameter('page'));
    }
    if($folder_id = $request->getParameter('folder_id'))
    {
      $this->getUser()->setAttribute('folder_id', $folder_id);
      $this->getUser()->setAttribute('page', 1);
    }
    $folder_id = $this->getUser()->getAttribute('folder_id', 0);

    if($folder_id)
    {
      $folder = Doctrine::getTable('lyMediaFolder')
        ->find($folder_id);
      $this->forward404Unless($folder);
      $this->folder = $folder;
    }
    else
    {
      //Root
      $this->folder = Doctrine::getTable('lyMediaFolder')
        ->getRoot();
    }
    $this->folders = $this->folder->getNode()->getChildren();

    if($request->hasParameter('sort'))
    {
      $this->getUser()->setAttribute('sort_field', $request->getParameter('sort'));
    }
    $this->sort_field = $this->getUser()->getAttribute('sort_field', 'name');

    if($request->hasParameter('dir'))
    {
      $this->getUser()->setAttribute('sort_dir', $request->getParameter('dir'));
    }
    $this->sort_dir = $this->getUser()->getAttribute('sort_dir');

    $this->pager = new sfDoctrinePager('lyMediaAsset', sfConfig::get('app_lyMediaManager_assets_per_page', 20));
    $this->pager->setQuery($this->folder->retrieveAssetsQuery(array(
      'sort_field' => $this->sort_field, 
      'sort_dir' => $this->sort_dir
    )));
    $this->pager->setPage($this->getUser()->getAttribute('page', 1));
    $this->pager->init();

    $this->popup = $request->getParameter('popup', 0);
    $this->getUser()->setAttribute('popup', $this->popup ? 1:0);

    $this->getUser()->setAttribute('view', 'icons');
  }

  /**
   * Deletes an asset.
   * 
   * @param sfWebRequest $request
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $this->getRoute()->getObject())));

    if ($this->getRoute()->getObject()->delete())
    {
      $this->getUser()->setFlash('notice', 'The item was deleted successfully.');
    }

    if($this->getUser()->getAttribute('view') == 'icons')
    {
      $this->redirect('@ly_media_asset_icons?folder_id=' . $this->getUser()->getAttribute('folder_id', 0) . ($this->getUser()->getAttribute('popup', 0) ? '&popup=1' : ''));
    }
    else
    {
      $this->redirect('@ly_media_asset');
    }
  }
}
