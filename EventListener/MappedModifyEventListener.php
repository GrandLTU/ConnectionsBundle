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

/**
 * Class MappedModifyEventListener.
 */
class MappedModifyEventListener extends AbstractImportModifyEventListener
{
    /**
     * @var array
     */
    private $map;

    /**
     * @param array $map EntityField => DocumentField.
     */
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    /**
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @param array $map
     *
     * @return $this
     */
    public function setMap(array $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function transform(array $document, $documentClass, $entity)
    {
        $map = $this->getMap();
        foreach ($map as $entityField => $documentField) {
            $entityFields = explode('.', $entityField);
            $data = $entity;
            foreach ($entityFields as $entityField) {
                $data = $this->getProperty($entityField, $data);
            }
            $document[$documentField] = $data;
        }

        return $document;
    }

    /**
     * Gets field from data.
     *
     * @param string       $field
     * @param array|object $data
     *
     * @return mixed
     */
    private function getProperty($field, &$data)
    {
        if (is_array($data)) {
            return isset($data[$field]) ? $data[$field] : null;
        }
        if (!is_object($data)) {
            throw new \InvalidArgumentException(
                'Invalid data. Expected object or array, got ' . gettype($data)
            );
        }
        $getter = 'get' . ucfirst($field);
        $isser = 'is' . ucfirst($field);
        $properties = get_object_vars($data);

        if (method_exists($data, $getter)) {
            return $data->{$getter}();
        } elseif (method_exists($data, $isser)) {
            return $data->{$isser}();
        } elseif (array_key_exists($field, $properties)) {
            return $data->{$entityField};
        } else {
            throw new \LogicException(
                sprintf(
                    'Object %s does not have %s or %s method and property %s is not public',
                    get_class($data),
                    $getter,
                    $isser,
                    $field
                )
            );
        }
    }
}
