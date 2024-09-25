<?php

namespace App\Controller;

use App\Repository\WishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WishController extends AbstractController
{
    #[Route('/wishes', name: 'wish_list', methods: ['GET'])]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy(['isPublished' => true], ['dateCreated' => 'DESC']);
        return $this->render('wish/list.html.twig', [
            "wishes" => $wishes,
        ]);
    }

    #[Route('/wishes/{id}', name: 'wish_detail', requirements:['id'=>'\d+'],methods: ['GET'])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        //Récupère ce wish en fonction de l'id présent dans l'url
        $wish = $wishRepository->find($id);
        //s'il n'existe pas en bdd on déclenche une erreur 404
        if(!$wish){
            throw $this->createNotFoundException('Wish not found');
        }
        return $this->render('wish/detail.html.twig', [
            "wish"=>$wish,
        ]);
    }
}
