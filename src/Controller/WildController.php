<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/wild")
 */
class WildController extends AbstractController
{
    /**
     * @Route("/", name="wild_index")
     * @return Response
     */
    public function index() :Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

    /**
     * param string $slug The slugger
     * @Route("/show/{slug}",
     *     requirements={"slug" = "[a-z0-9\-]+"},
     *     defaults={"slug" = "Aucune série sélectionnée, veuillez choisir une série"},
     *     name = "show")
     * @return Response
     */
    public function show(string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table');
        }
        $slug = preg_replace(
            '/-/',
            ' ',
            ucwords(trim(strip_tags($slug)), "-")
        );
        return $this->render(
            'wild/show.html.twig', [
            'slug' => $slug
        ]);
    }
}
