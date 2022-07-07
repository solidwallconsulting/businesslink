<?php

namespace App\Controller;

use App\Entity\ProductAddModel;
use App\Entity\Products;
use App\Form\ProductAddType;
use App\Form\ProductsType;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\ProductsRepository;
use App\Repository\ShopsRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/web-master/products")
 */
class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="products_index", methods={"GET"})
     */
    public function index(ProductsRepository $productsRepository, Request $request): Response
    {
        $error='';

        if ($request->query->get('error') != null) {
            $error = $request->query->get('error') ;
        }
       
        return $this->render('products/index.html.twig', [
            'products' => $productsRepository->findBy([],['addDate'=>'DESC']),
            'error'=>$error,
        ]);
    }

    /**
     * @Route("/new", name="products_new", methods={"GET","POST"})
     */
    public function new(ShopsRepository $shopsRepository, Request $request, CategoryRepository $categoryRepository, CityRepository $cityRepository): Response
    {
        $productAddModel = new ProductAddModel();

        $choices = [];

        $myShops = $shopsRepository->findBy(['owner'=>$this->getUser()->getId()]);
 

        $categories = $categoryRepository->findBy(['parent'=>null]); 
        for ($i=0; $i < sizeof($categories) ; $i++) { 
                $tmp = $categories[$i];

              if (sizeof($tmp->getSubCategories()) == 0 ) {
                    $choices[$tmp->getName()] = $tmp->getId();
              
              }else{
                  // go deeper
                  $subs = [];

                  for ($j=0; $j < sizeof($tmp->getSubCategories()) ; $j++) { 
                      $tmpSub = $tmp->getSubCategories()[$j];
                      $subs[$tmpSub->getName()]= $tmpSub->getId();
                  }

                  $choices[$tmp->getName()] = $subs;
              }
        }
  

        $form = $this->createForm(ProductAddType::class, $productAddModel,['categories'=>$choices,'shops'=>$myShops]);
        $form->handleRequest($request);

 
         

        if ($form->isSubmitted()) {

            $productAddModel->setStatus(0); 
            $productAddModel->setAddDate(new DateTime());


           
 
            /** @var UploadedFile $image */
             
            $image = $form->get('mainPhoto')->getData();
            
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try { 
                    $image->move('assets/products/',
                        $newFilename
                    );
                } catch (FileException $e) {
                     
                }
 
                $productAddModel->setMainPhoto('/assets/products/'.$newFilename);
            }else{
                $productAddModel->setMainPhoto('not-set');
            }
            /*************************************************************************** */
            /** @var UploadedFile $image */
             
            $image = $form->get('secondPhoto')->getData();
            
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try { 
                    $image->move('assets/products/',
                        $newFilename
                    );
                } catch (FileException $e) {
                     
                }
 
                $productAddModel->setSecondPhoto('/assets/products/'.$newFilename);
            }else{
                $productAddModel->setSecondPhoto('not-set');
            }
            /************************************************************************* */
            /** @var UploadedFile $image */
                        
            $image = $form->get('thirdPhoto')->getData();
                        
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try { 
                    $image->move('assets/products/',
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }

                $productAddModel->setThirdPhoto('/assets/products/'.$newFilename);
            }else{
                $productAddModel->setThirdPhoto('not-set');
            }


            $product = new Products();

            $product->setTitle($productAddModel->getTitle());
            $product->setDescription($productAddModel->getDescription());
            $product->setStatus(1);
            $product->setCategory( $categoryRepository->findOneBy(['id'=>$productAddModel->getCategory()]) );
            $product->setMainPhoto($productAddModel->getMainPhoto());
            $product->setSecondPhoto($productAddModel->getSecondPhoto());
            $product->setThirdPhoto($productAddModel->getThirdPhoto());
            $product->setAddDate(new DateTime());
            $product->setCity($cityRepository->findOneBy(['id'=>$productAddModel->getCity()]));
            $product->setOwner($this->getUser());
            $product->setPrice($productAddModel->getPrice());
          

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
 

           return $this->redirectToRoute('add_product_attribute_admin_route', ['id'=>$product->getId()], Response::HTTP_SEE_OTHER);
        } 

        return $this->renderForm('products/new.html.twig', [
            'product' => $productAddModel,
            'form' => $form ,
        ]);
    }

    /**
     * @Route("/{id}", name="products_show", methods={"GET","POST"})
     */
    public function show(Products $product,Request $request): Response
    {


        
        $method = $request->getMethod();

        if ($method =='POST') {
            if ($request->request->get('nextValue')) {

                if ($request->request->get('nextValue') == 'remove') {
                    $product->setStatus(0);
                    $this->getDoctrine()->getManager()->flush(); 
                }else{
                   $product->setStatus($request->request->get('nextValue'));
                $this->getDoctrine()->getManager()->flush(); 
                }
                

            
            }
        }
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="products_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Products $product): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'errorMessage'=>$request->query->get('errorMessage') != null ? $request->query->get('errorMessage') : ''
        ]);
    }

    /**
     * @Route("/delete-product-now-admin/{id}", name="products_delete", methods={"POST"})
     */
    public function delete(Request $request, Products $product): Response
    {
        try { 
            if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {



                // delete related attrs 

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($product);
                $entityManager->flush();

                return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
            }else{
                
            }
    
            
        } catch (\Throwable $th) {

             return $this->redirectToRoute('products_index', ['error'=>"Ce produit ne peut pas être supprimé car il est utilisé par les utilisateurs de l'application"], Response::HTTP_SEE_OTHER);
        }
    }
}
