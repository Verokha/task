<?php

namespace App\Service;


use App\Entity\News;
use App\Entity\TypeNews;
use App\Repository\NewsRepository;
use Doctrine\Persistence\ManagerRegistry;
use DOMDocument;
use DOMXPath;

class ParseService
{
    /** @var ManagerRegistry  */
    private $em;

    /** @var $typeNews TypeNews | null */
    private $typeNews = null;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine;
    }

    public function parseNews(TypeNews $typeNews)
    {
        $this->typeNews = $typeNews;
        $url = $typeNews->getLink();
        $DOMDocument = $this->getDOMDocument($this->getHtmlPageByUrl($url));
        $xPath = $this->getDOMXPath($DOMDocument);
        $lastDate = $this->getEM()->getRepository(News::class)->getLastDate();
        $news = [];

        /** @var DOMDocument $section */
        foreach ($xPath->query('//*[@class="news-feed__item js-news-feed-item js-yandex-counter"]') as $section) {
            if ($lastDate && $lastDate >= (int)$section->attributes->getNamedItem('data-modif')->nodeValue)
                continue;

            $tmpData['data_modif'] = $section->attributes->getNamedItem('data-modif')->nodeValue;
            $tmpData['link'] = $section->attributes->getNamedItem('href')->nodeValue;

            foreach ($xPath->query ('.//*[contains(@class, "news-feed__item__title")]', $section) as $review) {
                $tmpData['title'] = trim($review->nodeValue);
            }

            foreach ($xPath->query ('.//*[contains(@class, "news-feed__item__date-text")]', $section) as $review) {
                $explodeDateText = explode(',', $review->nodeValue);
                $tmpData['category'] = $explodeDateText[0];
                $tmpData['time'] = $explodeDateText[1];
            }
            $news[] = $tmpData;
        }

        foreach ($news as $key => $item) {
            $html = $this->getHtmlPageByUrl($item['link']);
            $linkWithoutGet = str_replace('?from=newsfeed', '', $item['link']);

            $simpleBlock = '<div class="l-col-main" data-io-article-url="' . $linkWithoutGet . '">';
            $anotherSimpleBlock = '<div class="l-col-main" data-io-article-url="' . $item['link'] . '">';
            $cryptoBlock = '<div class="article__content" data-io-article-url="'.$item['link'].'">';
            $anotherCryptoBlock = '<div class="article__content" data-io-article-url="'.$linkWithoutGet.'">';
            $trendsBlock = '<div class="article__text" itemprop="articleBody">';
            $mainLink = null;
            switch (true) {
                case (strpos($html, $simpleBlock) !== false):
                    $afterMainBlock = '<div class="article__tags">';
                    $mainBlock = $simpleBlock;
                    break;
                case (strpos($html, $anotherSimpleBlock) !== false):
                    $afterMainBlock = '<div class="article__tags">';
                    $mainBlock = $anotherSimpleBlock;
                    break;
                case (strpos($html, $cryptoBlock) !== false):
                    $afterMainBlock = '<div class="article__authors">';
                    $mainBlock = $cryptoBlock;
                    break;
                case (strpos($html, $anotherCryptoBlock) !== false):
                    $afterMainBlock = '<div class="article__authors">';
                    $mainBlock = $anotherCryptoBlock;
                    break;
                case (strpos($html, $trendsBlock) !== false):
                    $afterMainBlock = '<div class="article__authors">';
                    $mainBlock = $trendsBlock;
                    break;
                default:
                    continue 2;

            }
            $first_step = explode($mainBlock, $html);
            $second_step = explode($afterMainBlock, $first_step[1]);
            $finalBody = $second_step[0];
            $finalBody = str_replace("\n", "", $finalBody);
            $finalBody = str_replace("\r\n", "", $finalBody);
            $finalBody = str_replace("\r", "", $finalBody);
            $finalBody = trim($finalBody);

            $bodyDocument = $this->getDOMDocument('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$finalBody);
            $bodyXpath = $this->getDOMXPath($bodyDocument);
            $this->removeDivWithAD($bodyXpath);

            $previewText = $this->getPreviewText($bodyXpath);

            $finalBody = $bodyDocument->saveHTML();
            $news[$key]['body'] = $finalBody;
            $news[$key]['preview_text'] = $previewText;
        }

        return $news;

    }

    public function getHtmlPageByUrl($url)
    {
        $opts = [
            "ssl"=>[
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ],
            'http'=>[
                'method'=>"GET",
                'header'=> "User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36"
            ]
        ];

        $context = stream_context_create($opts);
        $site = file_get_contents($url, false, $context);

        return $site;
    }

    private function getEM()
    {
        return $this->em;
    }

    private function getTypeNews()
    {
        return $this->typeNews;
    }

    /**
     * @param string $html
     * @return DOMDocument
     */
    private function getDOMDocument(string $html): DOMDocument
    {
        $document = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_use_internal_errors($internalErrors);

        return $document;
    }

    /**
     * @param DOMDocument $DOMDocument
     * @return DOMXPath
     */
    private function getDOMXPath(DOMDocument $DOMDocument): DOMXPath
    {
        return new DOMXPath($DOMDocument);
    }

    /**
     * @param DOMXPath $selector
     */
    private function removeDivWithAD(DOMXPath &$selector)
    {
        $classes = explode(':', $this->getTypeNews()->getADClasses());
        foreach ($classes as $class) {
            foreach($selector->query('//div[contains(attribute::class, "'.$class.'")]') as $e ) {
                $e->parentNode->removeChild($e);
            }
        }

    }

    private function getPreviewText($selector)
    {
        $previewText = '';
        foreach($selector->query('//div[contains(attribute::class, "l-col-center-590 article__content")]') as $e ) {
            $previewText = mb_substr(trim($e->nodeValue), 0, 200).'...';
            break;
        }

        return $previewText;
    }

}