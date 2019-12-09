<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;

/**
 * @Route ("/wild")
 */
class WildController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/", name="wild_index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * @Route("/category/{categoryName}",
     *     name="show_category"),
     * @param string|null $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName)
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ',
            ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($categoryName)]);
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => ($category)],
                ['id' => 'DESC'],
                3
            );
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $categoryName . ' title, found in program\'s table.'
            );
        }
        return $this->render(
            'wild/category.html.twig', [
                'programs' => $program,
                'category' => $category
            ]
        );
    }

    /**
     * @Route("/show/{slug}", name="show_program")
     * @return Response
     */
    public function showByProgram(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy([
                'program' => $program,
            ]);
        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
            'seasons' => $season,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/season/{id<^[0-9-]+$>}", defaults={"id" = null}, name="show_season")
     */
    public function showBySeason(int $id): Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No season has been find in season\'s table.');
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($id);
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();
        if (!$season) {
            throw $this->createNotFoundException(
                'No season with ' . $id . ' season, found in Season\'s table.'
            );
        }
        return $this->render('wild/season.html.twig', [
            'seasons' => $season,
            'program' => $program,
            'episodes' => $episodes,
        ]);
    }

    /**
     *
     * @Route("/episode/{id}",  name="show_episode")
     *
     */
    public function showEpisode(Episode $episode) :Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        return $this->render('wild/episode.html.twig', [
            'episode'=>$episode,
            'seasons'=>$season,
            'program'=>$program,
        ]);
    }
}
