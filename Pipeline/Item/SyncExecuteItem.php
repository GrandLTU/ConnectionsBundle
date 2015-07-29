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
class SyncExecuteItem extends AbstractImportItem
{
    /**
     * @var array Sync storage data.
     */
    protected $syncStorageData;

    /**
     * @param mixed  $entity
     * @param array  $document
     * @param string $documentClass
     * @param array  $syncStorageData
     */
    public function __construct($entity, array $document, $documentClass, $syncStorageData)
    {
        parent::__construct($entity, $document, $documentClass);
        $this->syncStorageData = $syncStorageData;
    }

    /**
     * @return array
     */
    public function getSyncStorageData()
    {
        return $this->syncStorageData;
    }

    /**
     * @param array $syncStorageData
     */
    public function setSyncStorageData($syncStorageData)
    {
        $this->syncStorageData = $syncStorageData;
    }
}
