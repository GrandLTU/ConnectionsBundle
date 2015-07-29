<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ConnectionsBundle\Pipeline\Item;

/**
 * Import event item carrying both Doctrine entity and ES document.
 */
abstract class AbstractImportItem
{
    /**
     * @var mixed
     */
    protected $entity;

    /**
     * @var array
     */
    protected $document;

    /**
     * @var string
     */
    protected $documentClass;

    /**
     * @param mixed  $entity
     * @param array  $document
     * @param string $documentClass
     */
    public function __construct($entity, array $document, $documentClass)
    {
        $this->setEntity($entity);
        $this->setDocument($document);
        $this->setDocumentClass($documentClass);
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return array
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param array $document
     */
    public function setDocument(array $document)
    {
        $this->document = $document;
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
