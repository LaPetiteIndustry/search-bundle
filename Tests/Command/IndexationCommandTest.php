<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 28/04/15
 * Time: 11:49
 */

namespace Lpi\Bundle\LuceneBundle\Test\Command;


class IndexationCommandTest extends BaseCommandTest{
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
    public function testIndexation() {
        $client = self::createClient();
        $output = $this->runCommand($client, "lpi:lucene:index --path=sample-sitemap.xml");

        $this->assertContains('Indexation ended, will now optimize indexes', $output);
        $this->assertContains('Indexation ended with no error.', $output);
    }

    public function tearDown() {
        $this->client = self::createClient();
        unlink($this->client->getContainer()->getParameter('kernel.root_dir').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'sample-sitemap.xml');
    }
} 