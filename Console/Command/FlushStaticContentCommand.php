<?php

namespace Dgerken\EnhancedStaticContentDeploy\Console\Command;

use Dgerken\EnhancedStaticContentDeploy\Cache\StaticFileCache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for deploy static content
 */
class FlushStaticContentCommand extends Command
{
    /**
     *
     * Key for dry-run option
     */
    const COMMAND_NAME = 'setup:static-content:flush';

    /**
     * FileHashManager
     *
     * @var StaticFileCache
     */
    private $fileHashManager;

    /**
     * Inject dependencies
     *
     * @param StaticFileCache $fileHashManager
     */
    public function __construct(
        StaticFileCache $fileHashManager
    ) {
        $this->fileHashManager = $fileHashManager;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription('Flush static-content cache')
            ->setHelp('<info>' . self::COMMAND_NAME . '</info> flushes the static-content cache.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->fileHashManager->flushCache();
            
        } catch (\Exception $e) {
        }
    }
}
