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

use Doctrine\ORM\EntityManager;
use ONGR\ConnectionsBundle\Event\SyncImportSourceEvent;
use ONGR\ConnectionsBundle\Sync\Panther\Panther;
use ONGR\ElasticsearchBundle\ORM\Manager;

class SyncImportSourceEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var Manager
     */
    private $elasticSearchManager;

    /**
     * @var Panther
     */
    private $panther;

    /**
     * @var SyncImportSourceEvent
     */
    private $event;

    /**
     * Test SyncImportItem ChunkSize getter and setter.
     *
     * @return void
     */
    public function testChunkSizeGetterSetter()
    {
        $this->setUp();
        $data = 1;
        $this->event->setChunkSize($data);
        $result = $this->event->getChunkSize();
        $this->assertEquals($data, $result);
    }

    /**
     * Test SyncImportItem ShopId getter and setter.
     *
     * @return void
     */
    public function testShopIdGetterSetter()
    {
        $this->setUp();
        $data = 1;
        $this->event->setShopId($data);
        $result = $this->event->getShopId();
        $this->assertEquals($data, $result);
    }

    /**
     * Test SyncImportItem DocumentType getter and setter.
     *
     * @return void
     */
    public function testDocumentTypeGetterSetter()
    {
        $this->setUp();
        $data = 'product';
        $this->event->setDocumentType($data);
        $result = $this->event->getDocumentType();
        $this->assertEquals($data, $result);
    }

    /**
     * Prepares variables for test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->manager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->elasticSearchManager = $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->panther = $this->getMockBuilder('ONGR\ConnectionsBundle\Sync\Panther\Panther')
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = new SyncImportSourceEvent($this->manager, 'p', $this->elasticSearchManager, 'p', $this->panther);
    }
}
