<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ConnectionsBundle\EventListener;

use ONGR\ElasticsearchBundle\Service\Repository;

/**
 * Basic implementation of modifier. Only copies matching properties.
 */
class CopyModifyEventListener extends AbstractImportModifyEventListener
{
    /**
     * @var string[]
     */
    private $copySkipFields = [];

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository|null $repository
     */
    public function __construct(Repository $repository = null)
    {
        $this->repository = $repository;
    }

    /**
     * @return string[]
     */
    public function getCopySkipFields()
    {
        return $this->copySkipFields;
    }

    /**
     * @param string[] $copySkipFields
     *
     * @return $this
     */
    public function setCopySkipFields($copySkipFields)
    {
        $this->copySkipFields = $copySkipFields;

        return $this;
    }

    /**
     * Copies properties that have matching getter and setter.
     *
     * @param array    $document
     * @param string   $documentClass
     * @param object   $entity
     * @param string[] $skip
     *
     * @return array
     */
    protected function transform(array $document, $documentClass, $entity, $skip = null)
    {
        $entityMethods = get_class_methods($entity);
        $documentMethods = get_class_methods($this->getRepository()->getDocumentsClass($documentClass));
        $mapping = $this->getRepository()->getManager()->getBundlesMapping([$documentClass])[$documentClass];
        $aliases = $this->flipAliases($mapping->getAliases());

        if ($skip === null) {
            $skip = $this->getCopySkipFields();
        }

        foreach ($entityMethods as $method) {
            if (strpos($method, 'get') !== 0) {
                continue;
            }
            $property = substr($method, 3);
            $setter = 'set' . $property;
            $property = lcfirst($property);
            if (in_array($property, $skip)) {
                continue;
            }
            if (in_array($setter, $documentMethods)) {
                $document[$aliases[$property]] = $entity->{$method}();
            }
        }

        return $document;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        if ($this->repository === null) {
            throw new \LogicException('Manager must be set before using \'getRepository\'');
        }

        return $this->repository;
    }

    /**
     * @param Repository $repository
     *
     * @return $this
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Gets propertyName => FieldName array from aliases.
     *
     * @param array $aliases
     *
     * @return array
     */
    private function flipAliases($aliases)
    {
        $flipped = [];
        foreach ($aliases as $field => $alias) {
            $flipped[$alias['propertyName']] = $field;
        }

        return $flipped;
    }
}
