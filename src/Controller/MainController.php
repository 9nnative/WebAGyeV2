<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Album;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Form\CategoryType;
use App\Entity\Publication;
use App\Form\UserRightsType;
use App\Form\PublicationType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/publications/edit/{slug}", name="edit_publication")
     */
    public function addPublication(Request $request, Publication $publication = null): Response
    {
        if (!$publication){
        $publication = new Publication();
        }

        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $publication->setIsPublished(1);
            $date = new \DateTimeImmutable('now');
            $publication->setPublishedAt($date);
            $publication->setPublishedBy($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($publication);
            $entityManager->flush();

            $this->addFlash('success', 'Article créé !');
            
            return $this->redirectToRoute('all_publications');
        }

        
        return $this->render('publications/new.html.twig', [
            'publicationForm' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_EDITOR")
     * @Route("/publication/archive/{slug}", name="archive_publication")
     */
    public function archivePublication(Request $request, Publication $publication = null): Response
    {
        $state = $publication->getIsPublished();

        if ($state == 0){
            $publication->setIsPublished(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($publication);
            $entityManager->flush();
            header("Refresh:0");
            $this->addFlash('success', 'Article publié (public) !');
        } elseif ($state == 1){
            $publication->setIsPublished(0);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($publication);
            $entityManager->flush();
            header("Refresh:0");
            $this->addFlash('success', 'Article archivé !');
        }
        return $this->redirectToRoute('all_publications');
    }

    /**
     * @Route("/userdetails", name="userdetails")
     */
    public function userDetails(): Response
    {  
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
     * @Route("/allpublications", name="all_publications")
     */
    public function publicationsGallery(Request $request): Response
    {
        //findAll sur les publications
        $repository = $this->getDoctrine()->getRepository(Publication::class);
        $allpublications = $repository->findAll();

        //findall sur les catégories des publications
        $repositoryCategory =  $this->getDoctrine()->getRepository(Category::class);
        $allcategories = $repositoryCategory->findAll();

        //formulaire catégories des publications       
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie créée !');
            
            return $this->redirectToRoute('all_publications');
        }


        return $this->render('publications/index.html.twig', [
            'allpublications' => $allpublications,
            'allcategories' => $allcategories,
            'categoryForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/publication/{slug}", name="publication_details")
     */
    public function publiDetails(Publication $publication = null) : Response
    {

        return $this->render('publications/detail.html.twig', [
            'publication' => $publication,
        ]);
    }

}
