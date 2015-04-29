<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 29/04/15
 * Time: 18:49
 */

namespace Lpi\Bundle\SearchBundle\Model;


interface IndexableInterface {

    public function getId();
    public function getTitle();
    public function getSlug();
    public function getDescription();
} 