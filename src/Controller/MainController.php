<?php

namespace App\Controller;

use App\Entity\News;
use App\Service\ParseService;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
        $data = $parseService->parseNews('https://www.rbc.ru');
        foreach ($data as $item) {
            $news = new News();
            $news->setTitle($item['title'])
                ->setBody(isset($item['body']) ? $item['body'] : '')
                ->setRbclink($item['link'])
                ->setDatamodif($item['data_modif'])
                ->setPreviewText(isset($item['preview_text']) ? $item['preview_text'] : '');
            $this->getDoctrine()->getManager()->persist($news);
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
     */
    public function item(News $news)
    {
        return $this->render('main/item.html.twig', [
            'news' => $news
        ]);
    }
}
