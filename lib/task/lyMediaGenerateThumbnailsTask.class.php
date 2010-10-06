<?php
/*
 * This file is part of the lyMediaManagerPlugin package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * lyMediaGenerateThumbnailsTask class.
 *
 * (Re)generates thumbnails for assets inside a given folder or the whole media library.
 *
 * @package     lyMediaManagerPlugin
 * @subpackage  task
 * @copyright   Copyright (C) 2010 Massimo Giagnoni.
 * @license     http://www.symfony-project.org/license MIT
 * @version     SVN: $Id$
 */
class lyMediaGenerateThumbnailsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('folder', sfCommandArgument::OPTIONAL, 'Target folder (relative path), if empty media library root is assumed'),
    ));
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('no-recurse', null, sfCommandOption::PARAMETER_NONE, 'Generates thumbnails only for assets in target folder, excluding subfolders'),

    ));
    $this->namespace = 'media';
    $this->name = 'generate-thumbs';
    $this->briefDescription = '(Re)generates assets thumbnails';

    $this->detailedDescription = <<<EOF
The [media:generate-thumbs|INFO] command (re)generates assets thumbnails. It operates on the whole media library or on a given folder, recursively or not.

  [./symfony media:generate-thumbs |INFO]

Thumbnails are generated for all assets of the media library.

  [./symfony media:generate-thumbs media/photos|INFO]

Thumbnails are generated for assets inside the media/photos folder AND all subfolders.

  [./symfony media:generate-thumbs media/photos --no-recurse|INFO]

Thumbnails are generated ONLY for assets inside the media/photos folder.
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    if($arguments['folder'])
    {
      $folder = lyMediaFolderTable::getInstance()
        ->findOneByRelativePath(trim($arguments['folder'], DIRECTORY_SEPARATOR) . '/');
    }
    else
    {
      $folder = lyMediaFolderTable::getInstance()
        ->getRoot();
    }
    if(false === $folder)
    {
      throw new lyMediaException('Folder not found!');
    }
    if(!$this->askConfirmation(array(
      'Thumbnails will be generated for assets in the following media library folder:', '',
      $folder->getRelativePath() . ($options['no-recurse'] ? '' : ' and ALL subfolders recursively (use option --no-recurse to avoid it)'),
      '', 'Are you sure you want to proceed? (y/N)'
    ), 'QUESTION_LARGE', false))
    {
      $this->logSection('media', 'task aborted');
      return 1;
    }
    if($options['no-recurse'])
    {
      $folders = array($folder);
    }
    else
    {
      $folders = $folder->getNode()->getDescendants(null, true);
    }

    foreach($folders as $folder)
    {
      $this->logSection('media', sprintf('Working in %s ...', $folder->getRelativePath()), null, 'COMMENT');
      try
      {
        $folder->generateThumbnails();
      }
      catch (lyMediaException $e)
      {
        throw new sfException(strtr($e->getMessage(), $e->getMessageParams()));
      }
      $this->logSection('media', 'Done!');
    }
  }
}
