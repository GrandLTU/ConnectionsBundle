<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ConnectionsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command which checks sync storage for updated/created/deleted data and imports changes to elastic search.
 */
class SyncExecuteCommand extends ContainerAwareCommand
{
    use StartServiceHelperTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('ongr:sync:execute')->setDescription('Imports data from SyncStorage.');

        $this->addStandardArgument($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('ongr_connections.sync.execute_service');
        $this->start($input, $output, $service, 'sync.execute.');
    }
}
