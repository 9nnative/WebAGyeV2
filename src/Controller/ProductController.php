<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Product;
use App\Form\AlbumType;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProductController extends AbstractController
{
/**
 * @Route("/product/new", name="app_product_new")
 * @Route("/product/edit/{id}", name="app_product_edit")
 */
public function new(Request $request, SluggerInterface $slugger)
{
    $product = new Product();
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    $repository = $this->getDoctrine()->getRepository(Product::class);
    $allimages = $repository->findAll();


    if ($form->isSubmitted() && $form->isValid()) {
        // On récupère les images transmises
        $images = $form->get('brochure')->getData();
        
        // On boucle sur les images
        foreach($images as $image){
            
            // On génère un nouveau nom de fichier
            $fichier = md5(uniqid()).'.'.$image->guessExtension();
            
            // On copie le fichier dans le dossier uploads
            $image->move(
                $this->getParameter('images_directory'),
                $fichier
            );
            
            // On crée l'image dans la base de données
            $img = new Product();
            $img->setBrochureFilename($fichier);
            $img->setCategory($form->get('category')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($img);
            $entityManager->flush();

            $this->addFlash('uk-alert-primary', 'Image(s) publiée(s) !');
        }
    
        return $this->redirectToRoute('app_product_new');
    }

    return $this->render('product/new.html.twig', [
        'form' => $form->createView(),
        'allimages' => $allimages,
    ]);
}

/**
 * @Route("/supprime/image/{id}", name="annonces_delete_image", methods={"DELETE"})
 */
public function deleteImage(Product $image, Request $request){
    $data = json_decode($request->getContent(), true);

    // On vérifie si le token est valide
    if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
        // On récupère le nom de l'image
        $nom = $image->getBrochureFilename();
        // On supprime le fichier
        unlink($this->getParameter('images_directory').'/'.$nom);

        // On supprime l'entrée de la base
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        // On répond en json
        return new JsonResponse(['success' => 1]);
    }else{
        return new JsonResponse(['error' => 'Token Invalide'], 400);
    }
}

    /**
     * @Route("/album/new", name="add_album")
     */
    public function addAlbum(Request $request): Response
    {

        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($album);
            $entityManager->flush();

            $this->addFlash('success', 'Album créé ! Vous pouvez désormais y ajouter des photos.');

            return $this->redirectToRoute('app_product_new');
        }

        
        return $this->render('product/newCategory.html.twig', [
            'albumForm' => $form->createView(),
        ]);
    }

}
