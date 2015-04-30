<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 30/04/15
 * Time: 18:12
 */

namespace Lpi\Bundle\SearchBundle\Service;


class Mapper {
    protected $mappings;

    public function __construct($mappings) {
        $this->mappings = $mappings;
    }

    public function getMapper($class) {
        $classObject = substr($class, strrpos($class, '\\')+1);
        foreach ($this->mappings as $mapping) {
            if ($classObject === substr($mapping['value'], strrpos($mapping['value'], ':')+1)) {
                return $mapping;
            }
        }
    }
} 