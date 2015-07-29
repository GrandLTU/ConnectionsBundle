<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ConnectionsBundle\Tests\Unit\EventListener;

use ONGR\ConnectionsBundle\EventListener\SyncExecuteConsumeEventListener;
use ONGR\ConnectionsBundle\Pipeline\Event\ItemPipelineEvent;
use ONGR\ConnectionsBundle\Pipeline\Item\SyncExecuteItem;
use ONGR\ConnectionsBundle\Sync\ActionTypes;
use ONGR\ConnectionsBundle\Tests\Functional\Fixtures\ImportCommandTest\TestProduct;
use ONGR\ElasticsearchBundle\Client\Connection;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class SyncExecuteConsumeEventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests what notices are provided to logger in different cases.
     *
     * @param string $documentType
     * @param mixed  $eventItem
     * @param array  $loggerNotice
     * @param string $managerMethod
     *
     * @dataProvider onConsumeDataProvider
     */
    public function testOnConsume($documentType, $eventItem, $loggerNotice, $managerMethod)
    {
        $repo = $this->getMockBuilder('ONGR\ElasticsearchBundle\Service\Repository')
            ->disableOriginalConstructor()
            ->setMethods(['remove'])
            ->getMock();

        /** @var Connection|\PHPUnit_Framework_MockObject_MockObject $connection */
        $connection = $this->getMockBuilder('ONGR\ElasticsearchBundle\Client\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $manager = $this->getMockBuilder('ONGR\ElasticsearchBundle\Service\Manager')
            ->disableOriginalConstructor()
            ->setMethods(['persist', 'getRepository', 'getConnection', 'getBundlesMapping', 'persistRaw'])
            ->getMock();

        $manager->method('getRepository')
            ->willReturn($repo);

        $manager->method('getConnection')
            ->willReturn($connection);

        $manager->method('getBundlesMapping')
            ->willReturn(['AcmeTestBundle:Product' => null]);

        $syncStorage = $this->getMockBuilder('ONGR\ConnectionsBundle\Sync\SyncStorage\SyncStorage')
            ->disableOriginalConstructor()
            ->getMock();

        if ($managerMethod !== null) {
            $manager->expects($this->once())
                ->method($managerMethod);
        }

        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->setMethods(['log'])
            ->getMockForAbstractClass();

        $paramsArrays = [];

        foreach ($loggerNotice as $notice) {
            $paramsArrays[] = [$notice[1], $this->equalTo($notice[0]), []];
        }
        call_user_func_array(
            [
                $logger->expects($this->exactly(count($paramsArrays)))->method('log'),
                'withConsecutive',
            ],
            $paramsArrays
        );

        $listener = new SyncExecuteConsumeEventListener($manager, $documentType, $syncStorage, 1);
        $listener->setLogger($logger);

        $pipelineEvent = new ItemPipelineEvent($eventItem);
        $listener->onConsume($pipelineEvent);
    }

    /**
     * Provides data for testOnConsume test.
     *
     * @return array
     */
    public function onConsumeDataProvider()
    {
        $product = [
            '_id' => '123',
        ];

        return [
            [
                'document_type' => 'product',
                'event_item' => new SyncExecuteItem(
                    new TestProduct(),
                    $product,
                    'AcmeTestBundle:Product',
                    [
                        'type' => ActionTypes::DELETE,
                        'id' => 1,
                        'shop_id' => 1,
                    ]
                ),
                'logger_notice' => [
                    [
                        sprintf(
                            'Start update single document of type %s id: %s',
                            'AcmeTestBundle:Product',
                            123
                        ),
                        LogLevel::DEBUG,
                    ],
                    [
                        'End an update of a single document.',
                        LogLevel::DEBUG,
                    ],
                ],
                'managerMethod' => 'getRepository',
            ],
            [
                'document_type' => 'product',
                'event_item' => new SyncExecuteItem(
                    new TestProduct(),
                    $product,
                    'AcmeTestBundle:Product',
                    [
                        'type' => ActionTypes::UPDATE,
                        'id' => 1,
                        'shop_id' => 1,
                    ]
                ),
                'logger_notice' => [
                    [
                        sprintf(
                            'Start update single document of type %s id: %s',
                            'AcmeTestBundle:Product',
                            123
                        ),
                        LogLevel::DEBUG,
                    ],
                    [
                        'End an update of a single document.',
                        LogLevel::DEBUG,
                    ],
                ],
                'managerMethod' => 'persistRaw',
            ],
            [
                'document_type' => 'product',
                'event_item' => new SyncExecuteItem(
                    new TestProduct(),
                    $product,
                    'AcmeTestBundle:Product',
                    [
                        'type' => ActionTypes::CREATE,
                        'id' => 1,
                        'shop_id' => 1,
                    ]
                ),
                'logger_notice' => [
                    [
                        sprintf(
                            'Start update single document of type %s id: %s',
                            'AcmeTestBundle:Product',
                            123
                        ),
                        LogLevel::DEBUG,
                    ],
                    [
                        'End an update of a single document.',
                        LogLevel::DEBUG,
                    ],
                ],
                'managerMethod' => 'persistRaw',
            ],
            [
                'document_type' => 'product',
                'event_item' => new SyncExecuteItem(
                    new TestProduct(),
                    $product,
                    'AcmeTestBundle:Product',
                    ['type' => '']
                ),
                'logger_notice' => [
                    [
                        sprintf(
                            'Start update single document of type %s id: %s',
                            'AcmeTestBundle:Product',
                            123
                        ),
                        LogLevel::DEBUG,
                    ],
                    [
                        sprintf(
                            'Failed to update document of type  %s id: %s: no valid operation type defined',
                            'AcmeTestBundle:Product',
                            123
                        ),
                        LogLevel::DEBUG,
                    ],
                ],
                'managerMethod' => null,
            ],
            [
                'document_type' => 'product',
                'event_item' => new SyncExecuteItem(new TestProduct(), $product, 'AcmeTestBundle:Product', []),
                'logger_notice' => [['No operation type defined for document id: 123', LogLevel::ERROR]],
                'managerMethod' => null,
            ],
            [
                'document_type' => 'product',
                'event_item' => new \stdClass,
                'logger_notice' => [
                    ['Item provided is not an ONGR\ConnectionsBundle\Pipeline\Item\SyncExecuteItem', LogLevel::ERROR],
                ],
                'managerMethod' => null,
            ],
        ];
    }
}
