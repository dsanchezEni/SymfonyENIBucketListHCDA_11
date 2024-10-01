<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WishController extends AbstractController
{
    #[Route('/wishes', name: 'wish_list', methods: ['GET'])]
    public function list(WishRepository $wishRepository): Response
    {
        //$wishes = $wishRepository->findBy(['isPublished' => true], ['dateCreated' => 'DESC']);
        $wishes = $wishRepository->findPublishedWishesWithCategory();
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

    #[Route('/wishes/create', name: 'wish_create',methods: ['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $em,FileUploader $fileUploader): Response
    {
        $wish = new Wish();
        $wish->setUser($this->getUser());
        //On associe le formulaire à notre objet ici wish
        $wishForm=$this->createForm(WishType::class,$wish);
        //On récupére les données du form et on les injectent dans l'objet wish.
        $wishForm->handleRequest($request);
        //Si le formulaire est soumis et qu'il est valide
        if($wishForm->isSubmitted() && $wishForm->isValid()){
            $wish->setIsPublished(true);
            //Traitement de l'images
            /** @var UploadedFile $imageFile */
            $imageFile = $wishForm->get('image')->getData();
            if($imageFile){
                $wish->setFilename($fileUploader->upload($imageFile));
            }
            //Sauvegarde bdd
            $em->persist($wish);
            $em->flush();
            //Affiche le message
            $this->addFlash("success","Idea added successfully");
            //redirige ver la page de détail
            return $this->redirectToRoute('wish_detail',['id'=>$wish->getId()]);
        }
        //affiche le formulaire
        return $this->render('wish/create.html.twig', ['wishForm'=>$wishForm]);
    }

    #[Route('/wishes/{id}/update', name: 'wish_update',requirements:['id'=>'\d+'],methods: ['GET','POST'])]
    public function update(int $id, WishRepository $wishRepository,Request $request, EntityManagerInterface $em,FileUploader $fileUploader): Response
    {
        //Récupère ce wish en fonction de l'id présent dans l'url.
        $wish = $wishRepository->find($id);
        //S'il n'existe pas dans la bd on déclenche une erreur de type 404.
        if(!$wish){
            throw $this->createNotFoundException('Wish not found');
        }
        if(!($wish->getUser()===$this->getUser() ||  $this->isGranted('ROLE_ADMIN'))){
            throw $this->createAccessDeniedException();
        }
        //On associe le formulaire à notre objet ici wish
        $wishForm=$this->createForm(WishType::class,$wish);
        //On récupére les données du form et on les injectent dans l'objet wish.
        $wishForm->handleRequest($request);
        //Si le formulaire est soumis et qu'il est valide
        if($wishForm->isSubmitted() && $wishForm->isValid()){
            $wish->setDateUpdated(new \DateTimeImmutable());
            //Traitement de l'images
            $imageFile = $wishForm->get('image')->getData();
            if(($wishForm->has('deleteImage')&&$wishForm['deleteImage']->getData()) || $imageFile){
                //Suppression de l'ancienne images si on a coché l'option dans le formulaire
                //Ou si on a changé d'images.
                $fileUploader->delete($wish->getFilename(),$this->getParameter('app.images_wish_directory'));
                if($imageFile) {
                    $wish->setFilename($fileUploader->upload($imageFile));
                }else{
                    $wish->setFilename(null);
                }
            }

            //Sauvegarde bdd
            $em->persist($wish);
            $em->flush();
            //Affiche le message
            $this->addFlash("success","Idea updated successfully");
            //redirige ver la page de détail
            return $this->redirectToRoute('wish_detail',['id'=>$wish->getId()]);
        }
        //affiche le formulaire
        return $this->render('wish/create.html.twig', ['wishForm'=>$wishForm]);
    }

    #[Route('/wishes/{id}/delete', name: 'wish_delete',requirements:['id'=>'\d+'],methods: ['GET'])]
    public function delete(int $id, WishRepository $wishRepository,Request $request, EntityManagerInterface $em): Response
    {
        //Récupère ce wish en fonction de l'id présent dans l'url.
        $wish = $wishRepository->find($id);
        //S'il n'existe pas dans la bd on déclenche une erreur de type 404.
        if(!$wish){
            throw $this->createNotFoundException('Wish not found');
        }
        if($this->isCsrfTokenValid('delete'.$wish->getId(), $request->get('token'),)){
            $em->remove($wish,true);
            $em->flush();
            $this->addFlash("success","This wish has been deleted successfully");
        }else{
            $this->addFlash("danger","This wish cat not be deleted");
        }

        //affiche le formulaire
        return $this->redirectToRoute('wish_list');
    }

}
