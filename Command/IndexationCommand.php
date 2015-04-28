<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 27/04/15
 * Time: 19:14
 */

namespace Lpi\Bundle\LuceneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\SecurityBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;

class IndexationCommand extends ContainerAwareCommand {
    private $luceneIndex;

    public function configure() {
        $this
            ->setName('lpi:lucene:index')
            ->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Path to the sitemap.xml from the webroot dir.')
            ->setDescription("Lucene indexation from the sitemap file");
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $sitemapPath = $this->getContainer()->getParameter('kernel.root_dir').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$input->getOption('path');
        $this->luceneIndex = $this->getContainer()->get('ivory_lucene_search')->getIndex('search_index');

        try {
            if (is_file($sitemapPath)) {

                $xml = simplexml_load_file($sitemapPath);
                foreach ($xml as $entry) {
                    $loc = $entry->loc;
                    $result = $this->crawlRoute($loc);
                    if (isset($result['title']) && isset($result['content'])) {
                        $docs = $this->luceneIndex->find($result['title']);
                        if ($docs) {
                            foreach ($docs as $tmpDoc) {
                                if ($tmpDoc->title === $result['title']) {
                                    $this->luceneIndex->delete($tmpDoc->id);
                                }
                            }

                        }

                        $doc = new Document();
                        $doc->addField(Field::text('title', $result['title']));
                        $doc->addField(Field::text('content', $result['content']));

                        $this->luceneIndex->addDocument($doc);
                        $this->luceneIndex->commit();
                    }
                }
                $this->luceneIndex->optimize();


            } else {
                throw new FileNotFoundException(sprintf('Trying to load %s, but nothing found', $sitemapPath));
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            exit;
        }

    }

    /**
     * @param string $route
     * @return Crawler
     */
    private function crawlRoute($route) {
        $browser = $this->getContainer()->get('sonata.media.buzz.browser');
        $response = $browser->get($route);
        if ($response->getHeaders()[0] === 'HTTP/1.0 200 OK') {

            $crawler = new Crawler($browser->get($route)->getContent());
            $crawler->filterXPath(CssSelector::toXPath('div.container'))->text();

            $crawlResult =  array(
                'title' => $crawler->filter('head>title') ? $crawler->filter('head>title')->text() : null,
                'content' => $crawler->filterXPath(CssSelector::toXPath('div.container')) ? $crawler->filterXPath(CssSelector::toXPath('div.container'))->text() : null
            );
        } else {
            $crawlResult = array();
        }

        return $crawlResult;
    }
} 