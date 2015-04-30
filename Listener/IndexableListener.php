<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 30/04/15
 * Time: 18:02
 */

namespace Lpi\Bundle\SearchBundle\Listener;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Lpi\Bundle\SearchBundle\Model\IndexableInterface;
use Lpi\Bundle\SearchBundle\Service\SearchService;

class IndexableListener {

    protected $service;

    public function __construct(SearchService $service) {
        $this->service = $service;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getObject();
        if ($entity instanceof IndexableInterface) {
            $this->service->createIndex($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args) {
        $this->postPersist($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args) {
        $entity = $args->getObject();
        if ($entity instanceof IndexableInterface) {
            $this->service->removeIndex($entity->getTitle());
        }
    }
} 