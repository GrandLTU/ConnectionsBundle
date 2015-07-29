<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ConnectionsBundle\Tests\Functional\Fixtures\ImportCommandTest;

use ONGR\ConnectionsBundle\EventListener\AbstractImportModifyEventListener;

/**
 * Implementation of InitialSyncModifyEventListener.
 */
class TestModifyEventListener extends AbstractImportModifyEventListener
{
    /**
     * {@inheritdoc}
     */
    protected function transform(array $document, $documentClass, $entity)
    {
        $document['_id'] = $entity->id;
        $document['title'] = $entity->title;
        $document['price'] = $entity->price;
        $document['description'] = $entity->description;

        return $document;
    }
}
