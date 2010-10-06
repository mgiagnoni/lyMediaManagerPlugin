<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaFolder record class.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  model
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
abstract class PluginlyMediaFolder extends BaselyMediaFolder
{
  protected $parent = null;
  protected $old_path = null;

  /**
   * Creates a new folder.
   *
   * @param lyMediaFolder $parent, parent folder.
   */
  public function create(lyMediaFolder $parent)
  {
    if(!$this->getName())
    {
      throw new lyMediaException('Can\'t create a folder without name');
    }
    $this->setParent($parent);
    $this->save();
  }

  /**
   * (Re)generates thumbanails for all folder assets.
   */
  public function generateThumbnails()
  {
    foreach($this->getAssets() as $asset)
    {
      $asset->generateThumbnails();
    }
  }
  /**
   * Moves a folder under a new parent.
   *
   * @param lyMediaFolder $new_parent, new parent folder.
   */
  public function move(lyMediaFolder $new_parent)
  {
    $this->setParent($new_parent);
    $this->save();
  }

  /**
   * Returns folder name with indentation characters.
   *
   * @return string.
   */
  public function getIndentName()
  {
    return str_repeat('-- ', $this->level) . $this->name;
  }

  /**
   * Returns folder path.
   *
   * @return string.
   */
  public function getPath()
  {
    return $this->getNode()->getPath(DIRECTORY_SEPARATOR, true) . DIRECTORY_SEPARATOR;
  }

  /**
   * postInsert.
   *
   * @param Doctrine_Event $event.
   */
  public function postInsert($event)
  {
    if($this->getParent())
    {
      $this->getNode()->insertAsLastChildOf($this->getParent());
    }
  }

  /**
   * preDelete.
   *
   * @param Doctrine_Event $event.
   */
  public function preDelete($event)
  {
    $record = $event->getInvoker();
    $fs = new lyMediaFileSystem();
    if($fs->is_writable($record->getNode()->getParent()->getRelativePath()))
    {
      //Delete folder contents
      foreach($record->getAssets() as $a)
      {
        $a->delete();
      }
    }
    $fs->rmdir($record->getRelativePath());
  }

  /**
   * preInsert.
   *
   * @param Doctrine_Event $event.
   */
  public function preInsert($event)
  {
    $this->updateRelativePath();
    $fs = new lyMediaFileSystem();
    $fs->mkdir($this->getRelativePath());
  }

  /**
   * preUpdate.
   *
   * @param Doctrine_Event $event.
   */
  public function preUpdate($event)
  {
    if($this->old_path)
    {
      //Moved
      $fs = new lyMediaFileSystem();
      $fs->rename($this->old_path, $this->getRelativePath());
      $this->old_path = null;
      $this->getNode()->moveAsLastChildOf($this->parent);
    }
  }

  /**
   * Retrieves folder assets.
   *
   * @param array $params, sort parameters.
   * @return Doctrine_Collection.
   */
  public function retrieveAssets($params)
  {
    return $this->retrieveAssetsQuery($params)->execute();
  }

  /**
   * Generates query to retrieve folder assets.
   *
   * @param array $params, sort parameters.
   * @return Doctrine_Query.
   */
  public function retrieveAssetsQuery($params)
  {
    $by = $params['sort_field'] == 'date' ? 'created_at' : 'filename';
    $dir = $params['sort_dir'] == 'desc' ? ' desc' : '';

    return Doctrine_Query::create()
      ->from('lyMediaAsset a')
      ->where('a.folder_id = ?', $this->getId())
      ->orderBy($by . $dir);
  }

  /**
   * Sets parent folder.
   *
   * @param lyMediaFolder $folder.
   */
  public function setParent(lyMediaFolder $folder)
  {
    if(!isset($this->parent) || $this->parent->getId() != $folder->getId())
    {
      $this->parent = $folder;
      $this->updateRelativePath();
    }
  }

  /**
   * Gets parent folder.
   *
   * @return lyMediaFolder.
   */
  public function getParent()
  {
    if(isset($this->parent))
    {
      return $this->parent;
    }
    else
    {
      return $this->getNode()->getParent();
    }
  }

  /**
   * Used by synchronize task.
   *
   * @param string $baseFolder
   * @param bool $verbose
   * @param bool $removeOrphanAssets
   * @param bool $removeOrphanFolders
   */
  public function synchronizeWith($baseFolder, $verbose = true, $removeOrphanAssets = false, $removeOrphanFolders = false)
  {
    if (!is_dir($baseFolder))
    {
      throw new lyMediaException(sprintf('%s is not a directory', $baseFolder));
    }

    $files = sfFinder::type('file')->maxdepth(0)->ignore_version_control()->in($baseFolder);
    $assets = $this->getAssetsWithFilenames();
    $fs = new lyMediaFileSystem();
    foreach ($files as $file)
    {
      $basename = basename($file);
      if (!array_key_exists($basename, $assets))
      {
        // File exists, asset does not exist: create asset
        $fs->import($file, $this->getRelativePath() . $basename);
        $lyMediaAsset = new lyMediaAsset();
        $lyMediaAsset->setFolderId($this->getId());
        $lyMediaAsset->setFilename($basename);
        $lyMediaAsset->setType(mime_content_type($file));
        $lyMediaAsset->setFilesize(round(filesize($file) / 1024));
        $lyMediaAsset->save();
        if ($verbose)
        {
          lyMediaTools::log(sprintf("Importing file %s", $file), 'green');
        }
      }
      else
      {
        // File exists, asset exists: do nothing
        unset($assets[basename($file)]);
      }
    }

    foreach ($assets as $name => $asset)
    {
      if ($removeOrphanAssets)
      {
        // File does not exist, asset exists: delete asset
        $asset->delete();
        if ($verbose)
        {
          lyMediaTools::log(sprintf("Deleting asset %s", $asset->getPath()), 'yellow');
        }
      }
      else
      {
        if ($verbose)
        {
          lyMediaTools::log(sprintf("Warning: No file for asset %s", $asset->getPath()), 'red');
        }
      }
    }

    $dirs = sfFinder::type('dir')->maxdepth(0)->discard(lyMediaThumbnails::getThumbnailFolder())->ignore_version_control()->in($baseFolder);
    $folders = $this->getSubfoldersWithFolderNames();
    foreach ($dirs as $dir)
    {
      list(,$name) = lyMediaTools::splitPath($dir);
      if (!array_key_exists($name, $folders))
      {
        // dir exists in filesystem, not in database: create folder in database
        $lyMediaFolder = new lyMediaFolder();
        $lyMediaFolder->setName($name);
        $lyMediaFolder->create($this);
        if ($verbose)
        {
          lyMediaTools::log(sprintf("Importing directory %s", $dir), 'green');
        }
      }
      else
      {
        // dir exists in filesystem and database: look inside
        $lyMediaFolder = $folders[$name];
        unset($folders[$name]);
      }
      $lyMediaFolder->synchronizeWith($dir, $verbose, $removeOrphanAssets, $removeOrphanFolders);
    }

    foreach ($folders as $name => $folder)
    {
      if ($removeOrphanFolders)
      {
        $folder->delete(null, true);
        if ($verbose)
        {
          lyMediaTools::log(sprintf("Deleting folder %s", $folder->getRelativePath()), 'yellow');
        }
      }
      else
      {
        if ($verbose)
        {
          lyMediaTools::log(sprintf("Warning: No directory for folder %s", $folder->getRelativePath()), 'red');
        }
      }
    }
  }
  public function getAssetsWithFilenames()
  {
    $assets = $this->getAssets();
    $filenames = array();
    foreach($assets as $asset)
    {
      $filenames[$asset->getFilename()] = $asset;
    }
    return $filenames;
  }
  public function getSubfoldersWithFolderNames()
  {
    $foldernames = array();
    if($children = $this->getNode()->getChildren())
    {
      foreach ($children as $folder)
      {
        $foldernames[$folder->getName()] = $folder;
      }
    }

    return $foldernames;
  }

  /**
   * Returns total file size of folder assets.
   *
   * @return int
   */
  public function sumFileSizes()
  {
    return Doctrine_Query::create()
      ->select('SUM(a.filesize) size')
      ->from('lyMediaAsset a')
      ->where('a.folder_id = ?', $this->getId())
      ->fetchOne()
      ->getSize();
  }
  protected function updateRelativePath()
  {
    $relative_path = ($this->getParent() ? $this->getParent()->getRelativePath() : '') . $this->getName() . '/';

    if($this->getRelativePath() != $relative_path)
    {
      $this->old_path = $this->getRelativePath();
      $this->setRelativePath($relative_path);
    }
  }
}