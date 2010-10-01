<?php
class lyMediaSynchronizeTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('dirname', sfCommandArgument::REQUIRED, 'The name of the directory where the media files are located (relative or absolute)'),
    ));
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('verbose', null, sfCommandOption::PARAMETER_OPTIONAL, 'If true, every file or database operation will issue an alert in STDOUT', true),
      new sfCommandOption('removeOrphanAssets', null, sfCommandOption::PARAMETER_NONE, 'If true, database assets with no associated file are removed'),
      new sfCommandOption('removeOrphanFolders', null, sfCommandOption::PARAMETER_NONE, 'If true, database folders with no associated directory are removed'),
    ));
    $this->namespace = 'media';
    $this->name = 'synchronize';
    $this->briefDescription = 'Synchronize a physical folder content with the media library';

    $this->detailedDescription = <<<EOF
The [media:synchronize|INFO] synchronizes a physical folder content with the media library:

  [./symfony media:synchronize ./web/medias|INFO]

The command browses the folder recursively and adds every file found to the media library tables.
EOF;
  }
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    $rootFolder = lyMediaFolderTable::getInstance()
        ->getRoot();
    
    //TODO: check if media root has been created

    $this->logSection('media', sprintf('Comparing files from %s with assets stored in the database...', $arguments['dirname']), null, 'COMMENT');

    try
    {
      $rootFolder->synchronizeWith($arguments['dirname'], $options['verbose'], $options['removeOrphanAssets'], $options['removeOrphanFolders']);
    }
    catch (lyMediaException $e)
    {
      throw new sfException(strtr($e->getMessage(), $e->getMessageParams()));
    }

    $this->logSection('media', 'Synchronization complete', null, 'INFO');
  }
}