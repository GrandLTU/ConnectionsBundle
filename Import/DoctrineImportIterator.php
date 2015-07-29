<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ConnectionsBundle\Import;

use Doctrine\ORM\EntityManagerInterface;
use ONGR\ConnectionsBundle\Pipeline\Item\ImportItem;
use ONGR\ElasticsearchBundle\Service\Repository;
use Traversable;

/**
 * This class is able to iterate over entities without storing objects in doctrine memory.
 */
class DoctrineImportIterator extends \IteratorIterator
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var string
     */
    private $documentClass;

    /**
     * @param Traversable            $iterator
     * @param EntityManagerInterface $manager
     * @param Repository             $repository
     * @param string                 $documentClass
     */
    public function __construct(
        Traversable $iterator,
        EntityManagerInterface $manager,
        Repository $repository,
        $documentClass
    ) {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->documentClass = $documentClass;

        parent::__construct($iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $doctrineEntity = parent::current();

        return new ImportItem($doctrineEntity[0], [], $this->getDocumentClass());
    }

    /**
     * We need to clear identity map before navigating to next record.
     */
    public function next()
    {
        $this->manager->clear();
        parent::next();
    }

    /**
     * @return string
     */
    public function getDocumentClass()
    {
        return $this->documentClass;
    }

    /**
     * @param string $documentClass
     *
     * @return $this
     */
    public function setDocumentClass($documentClass)
    {
        $this->documentClass = $documentClass;

        return $this;
    }
}
