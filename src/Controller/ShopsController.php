<?php

namespace App\Controller;

use App\Entity\Shops;
use App\Form\ShopsType;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\ShopsRepository;
use App\Repository\TownRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 
class ShopsController extends AbstractController
{
    /**
     * @Route("/shops", name="shops_index", methods={"GET"})
     */
    public function index(Request $request, CityRepository $cityRepository,  CategoryRepository $categoryRepository, ShopsRepository $shopsRepository, TownRepository $townRepository ): Response
    {

        $city = $request->query->get('city');

        $categories = $categoryRepository->findBy(['parent'=>null]); 

        return $this->render('shops/index.html.twig', [
            'shops' => $shopsRepository->findAll(),
            "towns"=> $townRepository->findAll(),
            'keywords'=>'',
            "categories"=>$categories,
             
            'townID'=>$city == 0 ? null : $cityRepository->findOneBy(['id'=>$city])->getTown()->getId(),
        ]);
    }

    /**
     * @Route("/account/shop/new", name="shops_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $shop = new Shops();
 
        $form = $this->createForm(ShopsType::class, $shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $shop->setOwner($this->getUser());


            /** @var UploadedFile $image */
             
            $image = $form->get('shopBanner')->getData();
            
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try { 
                    $image->move('assets/shops/',
                        $newFilename
                    );
                } catch (FileException $e) {
                     
                }
 
                $shop->setShopBanner('/assets/shops/'.$newFilename);
            }else{
                $shop->setShopBanner('/assets/shops/default.png');
            }

              /** @var UploadedFile $image */
             
              $image = $form->get('logoURL')->getData();
            
              if ($image) {
                  $newFilename = uniqid().'.'.$image->guessExtension();
  
                  // Move the file to the directory where brochures are stored
                  try { 
                      $image->move('assets/shops/',
                          $newFilename
                      );
                  } catch (FileException $e) {
                       
                  }
   
                  $shop->setLogoURL('/assets/shops/'.$newFilename);
              }else{
                  $shop->setLogoURL('/assets/shops/default.png');
              }




            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($shop);
            $entityManager->flush();

            return $this->redirectToRoute('shops_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('shops/new.html.twig', [
            'shop' => $shop,
            'form' => $form,
        ]);
    }




    /**
     * @Route("/shops/search", name="shops_list_json", methods={"GET"})
     */
    public function shops_list_json(Request $request, ShopsRepository $shopsRepository): JsonResponse
    {
        

        $keywords = $request->query->get('query')!= null ? $request->query->get('query') : null;
        $category = $request->query->get('category')!= null ? $request->query->get('category') : null;
        $town = $request->query->get('town')!= null ? $request->query->get('town') : null;


        $entityManager = $this->getDoctrine()->getManager();


        $ql = "SELECT p FROM App\Entity\Shops p  WHERE p.id != 0 ";

 
        if ($keywords != null) {
            $ql.="AND p.label LIKE '%".$keywords."%' ";
        }

        if ($town !=  null) {
            $ql.="AND p.town = :town ";
        }

        
         

        $query = $entityManager->createQuery($ql);
        

        

        if ($town != null) { 
            $query->setParameter('town', $town);
        }

       
 

        $preList = $query->getResult();


        $list = [];

       if ($category != null) {
        foreach ($preList as $key => $tmp) {
            $categoriesSHOP = $tmp->getCategories();

            foreach ($categoriesSHOP as $key => $categoryLoc) {
                if ($categoryLoc->getId() == $category  ) {
                   array_push($list,$tmp);
                }
            }
        }

       }else{
           $list = $preList;
       }
        
        $shops = [];

        


        foreach ($list as $key => $shop) {
            $categoriesArray = [];

        foreach ($shop->getCategories() as $key => $value) {
            array_push($categoriesArray,array("name"=>$value->getName(),"id"=>$value->getId()));
        }
        
            array_push($shops,array( 
                "label"=>$shop->getLabel(),
                "bannerShop"=>$shop->getShopBanner(),
                "logoURL"=>$shop->getLogoURL(),
                "descreption"=>$shop->getDescription(),
                "email"=>$shop->getEmail(),
                "phone"=>$shop->getPhone(),
                "fix"=>$shop->getFixPhone(),
                "whatsapp"=>$shop->getWhatsAppNumber(),
                "town_id"=>$shop->getTown()->getId(),
                "town_label"=>$shop->getTown()->getName(),
                "categories"=>$categoriesArray,
                "url"=>$shop->getLink(),
                "id"=>$shop->getId(),
                "products"=>sizeof($shop->getProducts())
                
                
                
                
            ));
        }

        return $this->json($shops);

          


    }




    /**
     * @Route("/shops/details/{id}", name="shops_show", methods={"GET"})
     */
    public function show(Shops $shop): Response
    {
        return $this->render('shops/show.html.twig', [
            'shop' => $shop,
        ]);
    }


    /**
     * @Route("/account/my-shops", name="my_shops_route")
     */
    public function myshops(ShopsRepository $shopsRepository): Response
    {
        $res = $shopsRepository->findBy(['owner'=>$this->getUser()->getID()]);
        return $this->render('shops/my-shops.html.twig', [
            'shops' => $res,
        ]);
    }

    /**
     * @Route("/account/shops/{id}/edit", name="shops_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Shops $shop): Response
    {
        
        $form = $this->createForm(ShopsType::class, $shop,['edit'=>true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $shop->setOwner($this->getUser());


            /** @var UploadedFile $image */
             
            $image = $form->get('shopBanner')->getData();
            
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try { 
                    $image->move('assets/shops/',
                        $newFilename
                    );
                } catch (FileException $e) {
                     
                }
 
                $shop->setShopBanner('/assets/shops/'.$newFilename);
            } 

              /** @var UploadedFile $image */
             
              $image = $form->get('logoURL')->getData();
            
              if ($image) {
                  $newFilename = uniqid().'.'.$image->guessExtension();
  
                  // Move the file to the directory where brochures are stored
                  try { 
                      $image->move('assets/shops/',
                          $newFilename
                      );
                  } catch (FileException $e) {
                       
                  }
   
                  $shop->setLogoURL('/assets/shops/'.$newFilename);
              } 




              $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('shops_index', [], Response::HTTP_SEE_OTHER);
        }
 
        return $this->renderForm('shops/new.html.twig', [
            'shop' => $shop,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="shops_delete", methods={"POST"})
     */
    public function delete(Request $request, Shops $shop): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shop->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($shop);
            $entityManager->flush();
        }

        return $this->redirectToRoute('shops_index', [], Response::HTTP_SEE_OTHER);
    }
}
