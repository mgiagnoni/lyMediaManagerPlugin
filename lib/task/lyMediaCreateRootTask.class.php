<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaCreateRootTask class.
 *
 * Creates the media library root node and the corresponding root folder in
 * filesystem. Code taken from sfAssetsLibraryPlugin with minimal changes.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  task
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */

class lyMediaCreateRootTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace = 'media';
    $this->name = 'create-root';
    $this->briefDescription = 'Create a root node for the media library';

    $this->detailedDescription = <<<EOF
The [media:create-root|INFO] task creates a root node for the media library:

  [./symfony media:create-root|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    if (lyMediaFolderTable::getInstance()->getRoot())
    {
      throw new sfException('The media library already has a root');
    }

    $this->logSection('media-manager', sprintf('Creating root node at %s...', sfConfig::get('app_lyMediaManager_media_root', 'media')), null, 'COMMENT');
    lyMediaFolderTable::getInstance()
      ->createRoot(sfConfig::get('app_lyMediaManager_media_root', 'media'));
    
    $this->logSection('media-manager', 'Root Node Created', null, 'INFO');
  }
}