<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\TypeNews;
use App\Service\ParseService;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @param ParseService $parseService
     * @return Response
     * @Route("/", name="home")
     */
    public function index(ParseService $parseService): Response
    {
        $typeNews = $this->getDoctrine()->getRepository(TypeNews::class)->findAll();
        if (!$typeNews) {
            $typeNew = (new TypeNews())
                ->setName('РБК')
                ->setLink('https://www.rbc.ru')
                ->setADClasses('article__header js-article-header:article__ticker article__ticker_margin js-yandex-counter:article__inline-item:banner__median_mobile g-banner-hide-by-exclusive:article__padding-off:article__main-image__title:pro-anons:news-bar news-bar_article js-news-bar-desktop-top:article__authors:news-bar news-bar_article js-news-bar-desktop-bottom:l-col-right:l-row:l-row:bottom-menu:banner__filmstrip g-mobile-visible:banner__filmstrip g-tablet-visible:g-desktop:g-desktop-small:g-tablet:g-mobile:js-rbcslider-footer g-banner__news-footer g-relative:l-tr-news__flex__right:article__next-page js-news-next-el:article__tags')
                ->setBodyClassAfterBodyClass('<div class="l-col-main" data-io-article-url=".{{$linkWithoutGet}}.">:<div class="article__tags">~<div class="l-col-main" data-io-article-url=".{{$link}}.">:<div class="article__tags">~<div class="article__content" data-io-article-url=".{{$link}}.">:<div class="article__authors">~<div class="article__content" data-io-article-url=".{{$linkWithoutGet}}.">:<div class="article__authors">')
                ->setPreviewItemClass('news-feed__item js-news-feed-item js-yandex-counter:data_modif:link~news-feed__item__title:title~news-feed__item__date-text:category:time');
            $this->getDoctrine()->getManager()->persist($typeNew);
            $this->getDoctrine()->getManager()->flush();
            $typeNews[] = $typeNew;
        }
        foreach ($typeNews as $news) {
            $data = $parseService->parseNews($news);

            foreach ($data as $item) {
                $news = new News();
                $news->setTitle($item['title'])
                    ->setBody(isset($item['body']) ? $item['body'] : '')
                    ->setRbclink($item['link'])
                    ->setDatamodif($item['data_modif'])
                    ->setPreviewText(isset($item['preview_text']) ? $item['preview_text'] : '');
                $this->getDoctrine()->getManager()->persist($news);
            }
        }
        $this->getDoctrine()->getManager()->flush();
        $news = $this->getDoctrine()->getRepository(News::class)->getAllByDateDesc();


        return $this->render('main/index.html.twig', [
            'news' => $news
        ]);
    }

    /**
     * @param News $news
     * @Route("/news/{id}", name="news")
     * @ParamConverter("news", class="App\Entity\News")
     * @return Response
     */
    public function item(News $news)
    {
        return $this->render('main/item.html.twig', [
            'news' => $news
        ]);
    }

    /**
     * @return Response
     * @Route("/monitoring", name="monitoring")
     */
    public function getMonitoring()
    {
        $data = $this->getDoctrine()->getRepository(News::class)->findBy(['body' => ""]);

        return $this->render('main/monitoring.html.twig', [
            'data' => $data
        ]);
    }
}
