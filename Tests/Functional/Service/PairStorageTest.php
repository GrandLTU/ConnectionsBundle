<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ConnectionsBundle\Tests\Functional\Service;

use ONGR\ConnectionsBundle\Document\Pair;
use ONGR\ConnectionsBundle\Service\PairStorage;
use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PairStorageTest extends WebTestCase
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Tests the method set.
     */
    public function testSetPair()
    {
        $manager = new PairStorage(
            $this->manager
        );

        $manager->set('name0', 'test1');
        $manager->set('name1', 'test13');
        $pair1 = $this->getPair('name0', 'test1');
        $pair2 = $this->getPair('name1', 'test13');

        $repository = $this->manager->getRepository('ONGRConnectionsBundle:Pair');

        $search = $repository
            ->createSearch();
        $search->addQuery(new MatchAllQuery());
        $documents = $repository->execute($search);

        $expected = [$pair1, $pair2];

        $documents = iterator_to_array($documents);

        sort($documents);
        sort($expected);

        $this->assertEquals($expected, $documents);
    }

    /**
     * Function test removing a pair.
     */
    public function testRemove()
    {
        $manager = new PairStorage(
            $this->manager
        );
        $pair_value = $manager->get('name0');

        $this->assertEquals('will not be here', $pair_value);

        $manager->remove('name0');

        // Check if item was deleted.
        $pair_value = $manager->get('name0');

        $this->assertEquals(null, $pair_value);
    }

    /**
     * Test getter.
     */
    public function testGet()
    {
        $manager = new PairStorage(
            $this->manager
        );

        // This item must exist in db.
        $pair_value = $manager->get('name0');

        $this->assertEquals('will not be here', $pair_value);

        // This item is not existing.
        $pair_value = $manager->get('name13');

        $this->assertEquals(null, $pair_value);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->manager = $this->getServiceContainer()->get('es.manager');

        $this->manager->getConnection()->dropAndCreateIndex();

        // There is something wrong with ElasticsearchTestCase method getDataArray,
        // if we don't create in here all test data, it's not existing when test is run.
        $content = new Pair();
        $content->setId('name0');
        $content->setValue('will not be here');

        $this->manager->persist($content);
        $this->manager->commit();
    }

    /**
     * Returns service container, creates new if it does not exist.
     *
     * @return ContainerInterface
     */
    protected function getServiceContainer()
    {
        if ($this->container === null) {
            $this->container = self::createClient()->getContainer();
        }

        return $this->container;
    }

    /**
     * Creates pair model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return Pair
     */
    private function getPair($key, $value)
    {
        /** @var Pair $pair */
        $pair = new Pair();
        $pair->setId($key);
        $pair->setValue($value);
        $pair->setScore(1.0);

        return $pair;
    }
}
