<?php

namespace App\Command;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\ArticleAggregator\Api\FetchApi;
use App\Service\ArticleAggregator\Csv\FetchCsv;
use App\Service\ArticleAggregator\Rss\FetchRss;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-articles',
    description: 'Création de différents articles via une api, un flux rss et un fichier CSV',
)]
class CreateArticlesCommand extends Command
{
    const API = 'API';
    const API_CSV_PROPERTIES = ['title', 'author', 'content', 'publishedAt'];
    const API_URL = 'https://saurav.tech/NewsAPI/top-headlines/category/health/fr.json';
    const ARTICLES = 'articles';
    const CSV = 'CSV';
    const FILE_PATH = '%s/../File/articles.csv';
    const LE_MONDE = 'Le Monde';
    const RSS = 'RSS';
    const RSS_PROPERTIES = ['title', 'description', 'pubDate'];

    const RSS_URL = 'http://www.lemonde.fr/rss/une.xml';

    public function __construct(
        private FetchApi $fetchApi,
        private FetchRss $fetchRss,
        private FetchCsv $fetchCsv,
        private EntityManagerInterface $entityManager,
        private ArticleRepository $articleRepository
    )
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->createApiArticle();

        $this->createRssArticle();

        $this->createCsvArticle();

        $io->success('Vos articles ont bien été créé et enregistré en base de donnée');

        return Command::SUCCESS;
    }

    private function createApiArticle(): void
    {
        $content = $this->fetchApi->fetchApi(self::API_URL, self::API_CSV_PROPERTIES, self::ARTICLES);

        foreach ($content[self::ARTICLES] as $articleData) {
            if (null !== $this->duplicateChecker($articleData['title'])) {
                continue;
            }
            $article = new Article();
            $article->setTitle($articleData['title'])
                ->setAuthor($articleData['author'])
                ->setPublishedAt(new \DateTimeImmutable($articleData['publishedAt']))
                ->setContent($articleData['content'])
                ->setSource(self::API)
            ;
            $this->entityManager->persist($article);
        }
        $this->entityManager->flush();
    }

    private function createRssArticle(): void
    {
        $content = $this->fetchRss->fetchRss(self::RSS_URL, self::RSS_PROPERTIES);

        foreach ($content->channel->item as $articleData) {
            if (null !== $this->duplicateChecker($articleData->title)) {
                continue;
            }
            $article = new Article();
            $article->setTitle($articleData->title)
                ->setAuthor(self::LE_MONDE)
                ->setPublishedAt(new \DateTimeImmutable($articleData->pubDate))
                ->setContent($articleData->description)
                ->setSource(self::RSS)
            ;
            $this->entityManager->persist($article);
        }
        $this->entityManager->flush();
    }

    private function createCsvArticle(): void
    {
        $handle = $this->fetchCsv->fetchCsv(sprintf(self::FILE_PATH, __DIR__), self::API_CSV_PROPERTIES);

        fgetcsv($handle);
        while (($data = fgetcsv($handle, 1000, ';'))  !== false) {
            if (null !== $this->duplicateChecker($data[0])) {
                continue;
            }
            $article = new Article();
            $article->setTitle($data[0])
                ->setAuthor($data[1])
                ->setContent($data[2])
                ->setPublishedAt(new \DateTimeImmutable(trim($data[3], '" ')))
                ->setSource(self::CSV)
            ;
            $this->entityManager->persist($article);
        }
        fclose($handle);
        $this->entityManager->flush();
    }

    private function duplicateChecker($title): null|Article
    {
        return $this->articleRepository->findOneBy(['title' => $title]);
    }
}
