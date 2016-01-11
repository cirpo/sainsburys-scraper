<?php

namespace Sainsburys\Command;

use Sainsburys\Model\Url;
use Sainsburys\Service\ProductsInfoScraper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProductsScraperCommand.
 */
class ProductsScraperCommand extends Command
{
    const DEFAULT_URL = 'http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html';

    /**
     * @var ProductsInfoScraper
     */
    protected $productsInfoScraper;

    /**
     * ProductsScraperCommand constructor.
     *
     * @param ProductsInfoScraper $productsInfoScraper
     */
    public function __construct(ProductsInfoScraper $productsInfoScraper)
    {
        parent::__construct();
        $this->productsInfoScraper = $productsInfoScraper;
    }

    protected function configure()
    {
        $this->setName('products-scraper')
            ->setDescription('Sainsbury\'s product scraper')
            ->addOption('pretty')
            ->addArgument('url', InputArgument::OPTIONAL, 'Sainsbury\'s product list url to scrape');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $urlStr = $input->getArgument('url') ? $input->getArgument('url') : self::DEFAULT_URL;
        $url    = new Url($urlStr);

        try {
            $products = $this->productsInfoScraper->extract($url);
        } catch (\Exception $e) {
            return $output->writeln('Something wrong happened: '.$e->getMessage());
        }

        if (!$products->hasProducts()) {
            return $output->writeln('No products found');
        }

        if ($input->getOption('pretty')) {
            return $output->writeln(json_encode($products->toArray(), JSON_PRETTY_PRINT));
        }

        return $output->writeln(json_encode($products->toArray()));
    }
}
