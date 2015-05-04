<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 28/04/15
 * Time: 11:37
 */

namespace Lpi\Bundle\SearchBundle\Service;

use Ivory\LuceneSearchBundle\Model\LuceneManager;
use Lpi\Bundle\SearchBundle\Model\IndexableInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class SearchService {
    protected $index;
    protected $router;
    protected $mapper;
    protected $manager;

    /**
     * @param LuceneManager $lucene
     * @param Router $router
     * @param Mapper $mapper
     */
    public function __construct(LuceneManager $lucene, Router $router, Mapper $mapper) {
        $this->mapper = $lucene;
        $this->index = $lucene->getIndex('search_index');
        $this->router = $router;
        $this->mapper = $mapper;
    }

    /**
     * @param string $query
     * @return array
     */
    public function search($query) {
        if (is_array($query)) {
            $query = implode(' ', $query);
        }
        if (is_object($query)) {
            throw new InvalidParameterException('Parameter must be of type string or array object given!');
        }
        $result = array();
        if (null === $query || empty($query)) {
            return false;
        }
        $documents = $this->index->find('slug:'.str_replace(' ', '-', htmlentities($query)).' OR content: '.htmlentities($query));
        if ($documents) {
            foreach ($documents as $document) {
                if ($document->title && $document->url) {
                    $result[] = array(
                        'title' => $document->title,
                        'url' => $document->url
                    );
                }
            }
        }

        return $result;
    }

    /**
     * @param IndexableInterface $object
     */
    public function createIndex(IndexableInterface $object) {
        $this->findOrDeleteIndex($object->getTitle());
        $mapping = $this->mapper->getMapper(get_class($object));
        $title = $object->getTitle();
        $content = $object->getDescription();
        $routeParameters = $this->buildRouteParameters($object, $mapping['path']);
        $url = $this->router->generate($mapping['path'], $routeParameters);

        $this->createDocument($title, $content, $url);
    }

    /**
     * @param string $title
     */
    public function removeIndex($title) {
        $docs = $this->index->find(htmlentities(str_replace(' ', '-', $title)));
        if ($docs) {
            foreach ($docs as $tmpDoc) {
                if ($tmpDoc->title === str_replace(' ', '-', $title)) {
                    $this->index->delete($tmpDoc->id);
                }
            }
        }
    }

    public function clearIndex() {
        if ($this->manager instanceof LuceneManager) {
            $this->manager->eraseIndex('search_index');
        }
    }

    public function optimize() {
        $this->index->optimize();
    }
    /**
     * @param string $title
     */
    private function findOrDeleteIndex($title) {
        $this->removeIndex($title);
    }

    /**
     * @param string          $title
     * @param string          $content
     * @param string          $url
     * @param OutputInterface $output
     */
    private function createDocument($title, $content, $url) {
        $doc = new Document();
        $doc->addField(Field::text('slug', str_replace(' ', '-', $title)));
        $doc->addField(Field::text('title', $title));
        $doc->addField(Field::text('url', $url));
        $doc->addField(Field::text('content', $content));

        $this->index->addDocument($doc);
        $this->index->commit();
    }


    /**
     * @param IndexableInterface $object
     * @param string             $route
     * @return array
     */
    private function buildRouteParameters(IndexableInterface $object, $routeId) {
        $parameters = array();
        $route = $this->router->getRouteCollection()->get($routeId);
        $compiledRoute = $route->compile();
        $arguments = $compiledRoute->getVariables();
        if (count($arguments) > 0) {
            foreach ($arguments as $argument) {
                $getter = 'get'.ucfirst($argument);
                $parameters[$argument] = $object->$getter();
            }
        }

        return $parameters;
    }


} 
