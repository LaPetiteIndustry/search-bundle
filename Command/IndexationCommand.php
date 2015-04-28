<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 27/04/15
 * Time: 19:14
 */

namespace Lpi\Bundle\SearchBundle\Command;

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
    private $sitemap;

    public function __construct($sitemap) {
        $this->sitemap = $sitemap;
        parent::__construct();
    }

    public function configure() {
        $this
            ->setName('lpi:lucene:index')
            ->addOption('path', 'p', InputOption::VALUE_REQUIRED, 'Path to the sitemap.xml from the webroot dir.', $this->sitemap)
            ->addOption('selector', 'sel', InputOption::VALUE_REQUIRED, 'The css selector of the page.', 'div.container')
            ->setDescription("Lucene indexation from the sitemap file");
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $sitemapPath = $this->getContainer()->getParameter('kernel.root_dir').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$input->getOption('path');
        $this->luceneIndex = $this->getContainer()->get('ivory_lucene_search')->getIndex('search_index');

        try {
            if (is_file($sitemapPath)) {

                $xml = simplexml_load_file($sitemapPath);
                $output->writeln(sprintf('Sitemap has been loaded'));
                foreach ($xml as $entry) {
                    $loc = $entry->loc;
                    $result = $this->crawlRoute($loc, $input->getOption('selector'));
                    if (isset($result['title']) && isset($result['content'])) {
                        $output->writeln(sprintf('Find a resource : %s', $result['title']));
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
                        $doc->addField(Field::text('url', $loc));
                        $doc->addField(Field::text('content', $result['content']));

                        $this->luceneIndex->addDocument($doc);
                        $this->luceneIndex->commit();
                    }
                }
                $output->writeln(sprintf('Indexation ended, will now optimize indexes'));
                $this->luceneIndex->optimize();


            } else {
                throw new FileNotFoundException(sprintf('Trying to load %s, but nothing found', $sitemapPath));
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            exit;
        }
        $output->writeln(sprintf('<info>Indexation ended with no error.</info>'));

    }

    /**
     * @param string $route
     * @return Crawler
     */
    private function crawlRoute($route, $cssClass) {
        $browser = $this->getContainer()->get('sonata.media.buzz.browser');
        $response = $browser->get($route);
        if (preg_match('/HTTP\/1.(.) 200 OK/', $response->getHeaders()[0])) {
            $crawler = new Crawler($response->getContent());
            $title = $crawler->filter('head>title');
            $container = $crawler->filterXPath(CssSelector::toXPath($cssClass));
            $crawlResult =  array(
                'title' => $title ? $title->text() : null,
                'content' => $container ? $container->text() : null
            );
        } else {
            $crawlResult = array();
        }

        return $crawlResult;
    }
} 
