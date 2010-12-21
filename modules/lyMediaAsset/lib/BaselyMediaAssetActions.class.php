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
    if($request->getParameter('folder_id'))
    {
      $this->setFilters(array('folder_id' => $request->getParameter('folder_id')));
    }
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
    $this->folder = lyMediaFolderTable::getInstance()
      ->retrieveCurrent($folder_id);

    $this->forward404Unless($this->folder);
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

    if($request->hasParameter('hide'))
    {
      $this->getUser()->setAttribute('hide', $request->getParameter('hide') ? 1:0);
    }
    $this->hide = $this->getUser()->getAttribute('hide');

    $this->pager = new sfDoctrinePager('lyMediaAsset', sfConfig::get('app_lyMediaManager_assets_per_page', 20));
    $this->pager->setQuery($this->folder->retrieveAssetsQuery(array(
      'sort_field' => $this->sort_field, 
      'sort_dir' => $this->sort_dir
    )));
    $this->pager->setPage($this->getUser()->getAttribute('page', 1));
    $this->pager->init();

    if($request->getParameter('popup'))
    {
      $this->getUser()->setAttribute('popup', true);
    }
    else
    {
      $this->getUser()->getAttributeHolder()->remove('popup');
    }

    if($this->popup = $this->getUser()->getAttribute('popup'))
    {
      $this->setLayout($this->getContext()->getConfiguration()->getTemplateDir('lyMediaAsset', 'popupLayout.php') . DIRECTORY_SEPARATOR . 'popupLayout');
      $this->getResponse()->addJavascript('/lyMediaManagerPlugin/js/lymedia_popup.js');
      if($request->hasParameter('editor'))
      {
        $this->getUser()->setAttribute('editor', $request->getParameter('editor') == 'fck' ? 'fck' : 'tiny');
      }
      if($this->getUser()->getAttribute('editor') == 'fck')
      {
        $this->getResponse()->addJavascript('/lyMediaManagerPlugin/js/lymedia_fck_popup.js', 'last');
      }
      else
      {
        $this->getResponse()->addJavascript('tiny_mce/tiny_mce_popup');
        $this->getResponse()->addJavascript('/lyMediaManagerPlugin/js/lymedia_tiny_popup.js', 'last');
      }
      $this->getResponse()->addStyleSheet('/lyMediaManagerPlugin/css/lymedia_popup.css');
    }
    $this->getUser()->setAttribute('view', 'icons');

    $this->folder_form = new lyMediaCreateFolderForm();
    $this->upload_form = new lyMediaUploadForm(null, array('folder' => $this->folder));
    $this->nbfolders = $this->folders ? count($this->folders) : 0;
    $this->total_size = $this->folder->sumFileSizes();
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

    try
    {
      $this->getRoute()->getObject()->delete();
      $this->getUser()->setFlash('notice', 'The item was deleted successfully.');
    }
    catch(lyMediaException $e)
    {
      $this->getUser()->setFlash('error', strtr($e->getMessage(), $e->getMessageParams()));
    }

    switch($this->getUser()->getAttribute('view'))
    {
      case 'icons':
        $this->redirect('@ly_media_asset_icons?folder_id=' . $this->getUser()->getAttribute('folder_id', 0) . ($this->getUser()->getAttribute('popup', 0) ? '&popup=1' : ''));
        break;
      case 'folder':
        $this->redirect('@ly_media_folder');
      default:
        $this->redirect('@ly_media_asset');
    }
  }

  /**
   * Uploads an asset
   * 
   * @param sfWebRequest $request 
   */
  public function executeUpload(sfWebRequest $request)
  {
    $folder = lyMediaFolderTable::getInstance()
      ->retrieveCurrent($this->getUser()->getAttribute('folder_id', 0));

    $form = new lyMediaUploadForm(null, array(
      'folder' => $folder)
    );

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));

    if($form->isValid())
    {
      $form->save();
      $this->getUser()->setFlash('notice', 'File successfully uploaded.');

    }
    else
    {
      if($form['filename']->hasError())
      {
        $msg = 'Error on file name: ';
        $msg .= $form['filename']->getError()->getMessage();
      }
      if($form['folder_id']->hasError())
      {
        $msg = $form['folder_id']->getError()->getMessage();
      }
      elseif($form->hasGlobalErrors())
      {
        $errors = $form->getGlobalErrors();
        $msg = $errors[0]->getMessage();
      }
      $this->getUser()->setFlash('error', $msg);
    }
    $this->redirect('@ly_media_asset_icons?folder_id=' . $this->getUser()->getAttribute('folder_id', 0) . ($this->getUser()->getAttribute('popup', 0) ? '&popup=1' : ''));
  }

  /**
   * Deletes multiple assets (batch)
   *
   * @param sfWebRequest $request
   */
  protected function executeBatchDelete(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');

    $records = Doctrine_Query::create()
      ->from('lyMediaAsset')
      ->whereIn('id', $ids)
      ->execute();

    foreach ($records as $record)
    {
      try
      {
        $record->delete();
      }
      catch(lyMediaException $e)
      {
        $this->getUser()->setFlash('error', strtr($e->getMessage(), $e->getMessageParams()));
        $this->redirect('@ly_media_asset');
      }
    }

    $this->getUser()->setFlash('notice', 'The selected items have been deleted successfully.');
    $this->redirect('@ly_media_asset');
  }
  /**
   * Downloads an asset file
   * 
   * @param sfWebRequest $request 
   */
  public function executeDownload(sfWebRequest $request)
  {
    $asset = $this->getRoute()->getObject();
    $fs = new lyMediaFileSystem();
    $this->file = $fs->makePathAbsolute($asset->getPath());
    $response = $this->getResponse();
    $response->setHttpHeader('Content-Description', 'File Transfer');
    $response->setHttpHeader('Content-disposition', 'attachment; filename=' . $asset->getFilename());
    $response->setHttpHeader('Content-type', $asset->getType());
    $response->setHttpHeader('Content-Transfer-Encoding', 'binary');
    $response->setHttpHeader('Expires', 0);
    $response->setHttpHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
    $response->setHttpHeader('Content-Length', filesize($this->file));
    
    $this->setLayout(false);
  }

  /**
   * (Re)generates thumbnails for selected assets.
   *
   * @param sfWebRequest $request
   */
  protected function executeBatchGenerateThumbnails(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');

    $records = Doctrine_Query::create()
      ->from('lyMediaAsset')
      ->whereIn('id', $ids)
      ->execute();

    $ct = 0;
    foreach ($records as $record)
    {
      try
      {
        if($record->generateThumbnails())
        {
          $ct++;
        }
      }
      catch(lyMediaException $e)
      {
        $this->getUser()->setFlash('error', strtr($e->getMessage(), $e->getMessageParams()));
        $this->redirect('@ly_media_asset');
      }
    }
    if($ct > 0)
    {
      $this->getUser()->setFlash('notice', 'Thumbnails have been successfully generated for selected items.');
    }
    else
    {
      $this->getUser()->setFlash('error', 'None of selected items support thumbnails.');
    }
    $this->redirect('@ly_media_asset');
  }
}
