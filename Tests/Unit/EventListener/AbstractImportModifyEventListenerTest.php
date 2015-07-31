<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ConnectionsBundle\Tests\Unit\Event;

use ONGR\ConnectionsBundle\EventListener\AbstractImportModifyEventListener;
use ONGR\ConnectionsBundle\Pipeline\Event\ItemPipelineEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Tests what notice is provided.
 */
class AbstractImportModifyEventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests what notices are provided to logger in different cases.
     *
     * @param mixed  $eventItem
     * @param string $message
     * @param string $level
     *
     * @dataProvider onModifyDataProvider
     */
    public function testOnConsume($eventItem, $message, $level)
    {
        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->setMethods(['log'])
            ->getMockForAbstractClass();

        $logger->expects($this->once())
            ->method('log')
            ->with($level, $this->equalTo($message), []);

        /** @var AbstractImportModifyEventListener|\PHPUnit_Framework_MockObject_MockObject $listener */
        $listener = $this->getMockBuilder('ONGR\ConnectionsBundle\EventListener\AbstractImportModifyEventListener')
            ->getMockForAbstractClass();

        $listener->setLogger($logger);

        $pipelineEvent = new ItemPipelineEvent($eventItem);
        $listener->onModify($pipelineEvent);
    }

    /**
     * Provides data for testOnConsume test.
     *
     * @return array
     */
    public function onModifyDataProvider()
    {
        return [
            [
                new \stdClass,
                'The type of provided item is not ImportItem or SyncExecuteItem.',
                LogLevel::ERROR,
            ],
        ];
    }
}
