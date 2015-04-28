<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 28/04/15
 * Time: 11:45
 */

namespace Lpi\Bundle\SearchBundle\Test\Service;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class SearchServiceTest extends WebTestCase {

    /**
     * @group search
     */
    public function testSearch() {
        $client = self::createClient();

        $documents = $client->getContainer()->get('lpi_lucene.search')->search('Une vision pointue et composite');
        $this->assertGreaterThanOrEqual(1, count($documents));

    }
}
 
