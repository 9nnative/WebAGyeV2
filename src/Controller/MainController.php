<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Album;
use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\Publication;
use App\Form\UserRightsType;
use App\Form\PublicationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Publication::class);
        $allpublications = $repository->findAll();

        return $this->render('main/index.html.twig', [
            'allpublications' => $allpublications
        ]);
    }

    /**
     * @IsGranted("ROLE_EDITOR")
     * @Route("/publications/new", name="add_publication")
     */
    public function addPublication(Request $request): Response
    {

        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $date = new \DateTimeImmutable('now');
            $publication->setPublishedAt($date);
            $publication->setPublishedBy($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($publication);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé !');
            
            return $this->redirectToRoute('main');
        }

        
        return $this->render('publications/new.html.twig', [
            'publicationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/userdetails", name="userdetails")
     */
    public function userDetails(): Response
    {  
        $user=$this->getUser();
        $user->setRoles( array('ROLE_ADMIN') );
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();


        return $this->render('user/userdetails.html.twig');
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/userprivileges/edit/{id}", name="userprivileges")
     */
    public function userPrivileges(Request $request, User $user): Response
    {

        $form = $this->createForm(UserRightsType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
    
            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('allusers');
        }
        
        return $this->render('user/userprivileges.html.twig', [
            'usersForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/allusers", name="allusers")
     */
    public function allUsers(): Response
    {

        //findall sur les catégories
        $repositoryCategory =  $this->getDoctrine()->getRepository(User::class);
        $allusers = $repositoryCategory->findAll();

        return $this->render('user/allusers.html.twig', [
            'allusers' => $allusers,
        ]);
    }
    /**
     * @Route("/allimages", name="all_images")
     */
    public function imagesGallery(): Response
    {
        //findAll sur les images
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $allimages = $repository->findAll();

        //findall sur les catégories
        $repositoryCategory =  $this->getDoctrine()->getRepository(Album::class);
        $allalbums = $repositoryCategory->findAll();

        return $this->render('product/index.html.twig', [
            'allimages' => $allimages,
            'allalbums' => $allalbums
        ]);
    }

    /**
     * @Route("/publication/{title}", name="publication_details")
     */
    public function publiDetails(Publication $publication = null ) : Response
    {

        return $this->render('publications/detail.html.twig', [
            'publication' => $publication,
        ]);
    }

}
