<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 28/04/15
 * Time: 11:37
 */

namespace Lpi\Bundle\SearchBundle\Service;

use Ivory\LuceneSearchBundle\Model\LuceneManager;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class SearchService {
    protected $index;

    public function __construct(LuceneManager $lucene) {
        $this->index = $lucene->getIndex('search_index');
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
        $documents = $this->index->find($query);
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

} 
