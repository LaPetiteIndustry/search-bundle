<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 28/04/15
 * Time: 11:45
 */

namespace Lpi\Bundle\LuceneBundle\Test\Service;


use Symfony\Bundle\SecurityBundle\Tests\Functional\WebTestCase;

class SearchServiceTest extends WebTestCase {
    protected $client;
    public function setUp() {
        $this->client = self::createClient();
        copy(
            __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.'sample-sitemap.xml',
            $this->client->getContainer()->getParameter('kernel.root_dir').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'sample-sitemap.xml'
        );
    }

    /**
     * @group search
     */
    public function testSearch() {

    }

    public function tearDown() {
        $this->client = self::createClient();
        unlink($this->client->getContainer()->getParameter('kernel.root_dir').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'sample-sitemap.xml');
    }
}
 