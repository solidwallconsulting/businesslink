<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ChatMessages;
use App\Entity\Conversation;
use App\Entity\FavouritsProducts;
use App\Entity\ProductAddModel;
use App\Entity\ProductAttributesValues;
use App\Entity\Products;
use App\Entity\Town;
use App\Entity\User;
use App\Form\ProductAddType;
use App\Form\ProductsType;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use App\Repository\AttributeValuesRepository;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\ConversationRepository;
use App\Repository\FavouritsProductsRepository;
use App\Repository\ProductsRepository;
use App\Repository\ShopsRepository;
use App\Repository\TownRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
 
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppController extends AbstractController
{

    private $encoder;
 
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


   

    /**
     * @Route("/city/town/{id}", name="get_city_by_twon_id")
     */
    public function findCityByTownID($id, CityRepository $cityRepository): JsonResponse
    {

        $res = $cityRepository->findBy(['town'=>$id]);

        $cities = [];

        foreach ($res as $key => $value) {
            array_push($cities,['id'=>$value->getId(),'name'=>$value->getName()]);
        }
 

        return $this->json($cities);
    }


    /**
     * @Route("/faq", name="faq_route")
     */
    public function faq_route( ): Response
    {
        return $this->render('app/faq.html.twig', [ 
        ]);
    }



    



    /**
     * @Route("/", name="main_route")
     */
    public function index(ArticleRepository $articleRepository, FavouritsProductsRepository $favouritsProductsRepository , Request $request,CategoryRepository $categoryRepository,ProductsRepository $productsRepository,TownRepository $townRepository): Response
    {
        $articles = $articleRepository->findBy([],['addDate'=>'DESC']);



                // favourit add
                $method = $request->getMethod();
                if ($method == 'POST') {
                    $params = $request->request;
        
                    if ($params->get('product-add-favourite') != null) {
                        $idProduct = $params->get('product-add-favourite');
                        $prod = $productsRepository->findOneBy(['id'=>$idProduct]);
        
                        // check if user already have this prod in his favs list 
                        $res = $favouritsProductsRepository->findOneBy(['user'=>$this->getUser(),'product'=>$prod]);
                        if ($res == null) {
                            $tmp = new FavouritsProducts();
                            $tmp->setUser($this->getUser());
                            $tmp->setProduct($prod);
        
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($tmp);
                            $entityManager->flush();
        
        
                        }
        
                    }
        
                }


        $searchQuery = $request->get('keyWords'); 

        $entityManager = $this->getDoctrine()->getManager(); 
        $orderBy = ($request->get('order'));
 
        $ql = 'SELECT p FROM App\Entity\Products p WHERE p.status = 1  ORDER BY p.addDate DESC ';

        $query = $entityManager->createQuery($ql)->setFirstResult(0)
        ->setMaxResults(3);

        $products = $query->getResult();


        if ($searchQuery != null) { 

            return $this->redirectToRoute('all_products_route', ['keyWords'=>$searchQuery], Response::HTTP_SEE_OTHER);
        }

        $categories = $categoryRepository->findBy(['parent'=>null]); 
        return $this->render('app/index.html.twig', [
            'categories'=>$categories,
            'products'=>  $products,
            'towns'=>$townRepository->findAll( ),
            'articles'=>$articles
        ]);
    }



    /**
     * @Route("/blog/articles/{id}", name="article_details_route")
     */
    public function articleDetails(Article $article): Response
    {
      
        return $this->render('app/article-details.html.twig', [
            'article'=>$article 
        ]);
    }





    /**
     * @Route("/account/product/add", name="add_product_route", methods={"GET","POST"})
     */
    public function addProduct(ShopsRepository $shopsRepository, Request $request,CategoryRepository $categoryRepository,CityRepository $cityRepository): Response
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
                $productAddModel->setMainPhoto('/assets/products/null.png');
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
                $productAddModel->setSecondPhoto('/assets/products/null.png');
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
                $productAddModel->setThirdPhoto('/assets/products/null.png');
            }


            $product = new Products();

            $product->setTitle($productAddModel->getTitle());
            $product->setDescription($productAddModel->getDescription());
            $product->setStatus($productAddModel->getStatus());
            $product->setCategory( $categoryRepository->findOneBy(['id'=>$productAddModel->getCategory()]) );
            $product->setMainPhoto($productAddModel->getMainPhoto());
            $product->setSecondPhoto($productAddModel->getSecondPhoto());
            $product->setThirdPhoto($productAddModel->getThirdPhoto());
            $product->setAddDate(new DateTime());
            $product->setCity($cityRepository->findOneBy(['id'=>$productAddModel->getCity()]));
            $product->setOwner($this->getUser());
            $product->setPrice($productAddModel->getPrice());
            $product->setShop($productAddModel->getShop());
 
 

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
 
            return $this->redirectToRoute('add_product_attribute_route', ['id'=>$product->getId()], Response::HTTP_SEE_OTHER);
        }



        return $this->render('app/add-product.html.twig', [
            'product' => $productAddModel,
            'form' => $form->createView(),

        ]);
    }



    /**
     * @Route("/account/product/attributes/add/{id}", name="add_product_attribute_route", methods={"GET","POST"})
     */
    public function productAttributesAdd(Products $product, Request $request,CategoryRepository $categoryRepository,ProductsRepository $productsRepository,AttributeValuesRepository $attributeValuesRepository): Response
    {
 
        $attributes = $product->getCategory()->getAttributeCategories();

        

        $parameters = $request->request;  

        $method = $request->getMethod();
         
        

        /**
         * "attributes-1" => "1"
         * "attributes-2" => "3"
         * "attributes-3" => "5"
         */


        if ($method == 'POST') {

            // first we delete the old ones <!DOCTYPE html>

            $oldList = $product->getProductAttributesValues();
            $entityManager = $this->getDoctrine()->getManager();
            
            

            foreach ($oldList as $cm) {
                
                $entityManager->remove($cm);
                $entityManager->flush();
            }
             
            for ($i=0; $i < sizeof($parameters) ; $i++) { 
                if ( $parameters->get('attributes-'.($i+1)) != "" ) {
                   $attributeValue = $attributeValuesRepository->findOneBy(['id'=>$parameters->get('attributes-'.($i+1))]);
                    
                   $tmp = new ProductAttributesValues();
                   $tmp ->setProduct($product);
                   $tmp->setAttribute($attributeValue);
   
                   $entityManager = $this->getDoctrine()->getManager();
                   $entityManager->persist($tmp);
                   $entityManager->flush();
   
   
                   // affect 
               }
                
            }
   
   
            return $this->redirectToRoute('my_products_route', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('app/product-attributes.html.twig', [
             'product'=>$product,
             'attributes'=>$attributes
        ]);
    }





    /**
     * @Route("/account/product/edit/{id}", name="edit_product_route", methods={"GET","POST"})
     */
    public function editProduct(   Products $product,  Request $request,CategoryRepository $categoryRepository,CityRepository $cityRepository, ShopsRepository $shopsRepository): Response
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

        $productAddModel->setTitle($product->getTitle());
        $productAddModel->setDescription($product->getDescription());
        $productAddModel->setCategory($product->getCategory()->getId());
        $productAddModel->setStatus($product->getStatus());
        $productAddModel->setMainPhoto($product->getMainPhoto());
        $productAddModel->setSecondPhoto($product->getSecondPhoto());
        $productAddModel->setThirdPhoto($product->getThirdPhoto());
        $productAddModel->setPrice($product->getPrice());
        $productAddModel->setAddDate($product->getAddDate());
        $productAddModel->setCity($product->getCity());
        $productAddModel->setTown($product->getCity()->getTown());

        $productAddModel->setId($product->getId());
        $productAddModel->setShop($product->getShop());
        
  

        $form = $this->createForm(ProductAddType::class, $productAddModel,['categories'=>$choices,'shops'=>$myShops,'isEditing'=>true]);
        $form->handleRequest($request);



        $method = $request->getMethod();
        if ($method == 'POST') {
            $params = $request->request;

            if ($params->get('product-sold') =="product-sold") {
                $product->setStatus(3);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('my_products_route', [], Response::HTTP_SEE_OTHER);
            }
        }


 
         

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
            } 


             
            $product->setTitle($productAddModel->getTitle());
            $product->setDescription($productAddModel->getDescription());
             
            $product->setCategory( $categoryRepository->findOneBy(['id'=>$productAddModel->getCategory()]) );
            $product->setMainPhoto($productAddModel->getMainPhoto());
            $product->setSecondPhoto($productAddModel->getSecondPhoto());
            $product->setThirdPhoto($productAddModel->getThirdPhoto());
             
            $product->setCity($cityRepository->findOneBy(['id'=>$productAddModel->getCity()]));
             
            $product->setPrice($productAddModel->getPrice());

 

            $this->getDoctrine()->getManager()->flush();

            
            

            return $this->redirectToRoute('my_products_route', [], Response::HTTP_SEE_OTHER);
        }



        return $this->render('app/edit-product.html.twig', [
            'product' => $productAddModel,
            'form' => $form->createView(),

        ]);
    }





    /**
     * @Route("/account", name="profile_route")
     */
    public function profile(): Response
    {
        // check if account is confirmer
        if ($this->getUser()->getIsActive() == false) {
            $this->get('security.token_storage')->setToken(null);  $this->get('session')->invalidate();
            return $this->redirectToRoute('app_login', ['message'=>"Votre compte n'est pas encore activé."], Response::HTTP_SEE_OTHER);
        }
        return $this->render('app/profile.html.twig', []);
    }

    /**
     * @Route("/account/my-products/{status}", name="my_products_route")
     */
    public function my_products_route(?int $status = 99,ProductsRepository $productsRepository): Response
    {
        $products = ($status == 99) ? $productsRepository->findBy(['owner'=>$this->getUser()])  : $productsRepository->findBy(['owner'=>$this->getUser(),'status'=>$status]);


        return $this->render('app/my-products.html.twig', [
            'status'=>$status,
            'products'=>$products
        ]);
    }


    /**
     * @Route("/account/my-favs-products", name="my_favs_products_route")
     */
    public function my_favs_products_route(?int $status = 99,FavouritsProductsRepository $favouritsProductsRepository): Response
    {
        $favourits = $this->getUser()->getFavouritsProducts();

        return $this->render('app/my-favs-products.html.twig', [
            'status'=>$status,
            'favourits'=>$favourits
        ]);
    }


    
    /**
     * @Route("/account/delete-my-fav/{id} ", name="remove_from_my_favs")
     */
    public function remove_from_my_favs(FavouritsProducts $favouritsProducts): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($favouritsProducts);
            $entityManager->flush();

        return $this->redirectToRoute('my_favs_products_route', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/products/{pageIndex}/{category}/{minPrice}/{maxPrice}/{city}/{keyWords}", name="all_products_route")
     */
    public function all_products_route( FavouritsProductsRepository $favouritsProductsRepository, int $pageIndex = 1, Request $request,?int $category = 0 , ?string $keyWords ='', float $maxPrice = 250000 ,float $minPrice = 0 , ?int $city = 0, ProductsRepository $productsRepository, CategoryRepository $categoryRepository, TownRepository $townRepository, CityRepository $cityRepository,
     PaginatorInterface $paginator 
    ): Response
    {

        // favourit add
        $method = $request->getMethod();
        if ($method == 'POST') {
            $params = $request->request;

            if ($params->get('product-add-favourite') != null) {
                $idProduct = $params->get('product-add-favourite');
                $prod = $productsRepository->findOneBy(['id'=>$idProduct]);

                // check if user already have this prod in his favs list 
                $res = $favouritsProductsRepository->findOneBy(['user'=>$this->getUser(),'product'=>$prod]);
                if ($res == null) {
                    $tmp = new FavouritsProducts();
                    $tmp->setUser($this->getUser());
                    $tmp->setProduct($prod);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($tmp);
                    $entityManager->flush(); 
                }

            }

        }

        $products = $productsRepository->findBy(['status'=>1]); 


        $entityManager = $this->getDoctrine()->getManager();


        $ql = "SELECT p FROM App\Entity\Products p WHERE p.status = 1 ";

 
        if ($category != 0) {
            $ql.="AND p.category = :idCategory ";
        }

        if ($maxPrice != 0) {
            $ql.="AND p.price <= :maxPrice ";
        }

         

        if ($minPrice != 0) {
            $ql.="AND p.price >= :minPrice ";
        }

        if ($keyWords != '') {
            $ql.="AND p.title LIKE '%".$keyWords."%' ";
        }

        if ($city != 0) {
            $ql.="AND p.city = :idCity ";
        }

        

        $orderBy = ($request->get('order'));

        if ($orderBy != null) {
            if ($orderBy == 'addDateOldest') {
                $ql.="ORDER BY p.addDate ASC";
            }
            if ($orderBy == 'addDateRecents') {
                $ql.="ORDER BY p.addDate DESC";
            }
            
            if ($orderBy == 'price') {
                $ql.="ORDER BY p.price ASC";
            }
            
        }else{
            // default by date, latest
            $ql.="ORDER BY p.addDate DESC";
        }

        
        

        

        $query = $entityManager->createQuery($ql);
        

        if ($category != 0) {
          
            $query->setParameter('idCategory', $category) ;
        }

        if ($maxPrice != 0) {
            
            $query->setParameter('maxPrice', $maxPrice);
        }

        if ($minPrice != 0) {

            $query ->setParameter('minPrice', $minPrice);
             
        }


        if ($city != 0) { 
            $query ->setParameter('idCity', $city);
        }
         

        $list = $query->getResult();

        

        // Paginate the results of the query
        $listProducts = $paginator->paginate( 
            $query, 
            $request->query->getInt('page', $pageIndex), 
            12
        );


        



        $categories = $categoryRepository->findBy(['parent'=>null]); 
        $categoryLabel = $categoryRepository->findOneBy(['id'=>$category]); 
   
        return $this->render('app/all-products.html.twig', [
            'products'=>$listProducts,
            'categories'=>$categories,
            'minPrice'=>$minPrice,
            'maxPrice'=>$maxPrice,
            'category'=>$category,
            'categoryLabel'=>$categoryLabel,
            'keywords'=>$keyWords,
            'city'=>$city,
            'town'=>$city == 0 ? null : $cityRepository->findOneBy(['id'=>$city])->getTown(),
            'townID'=>$city == 0 ? null : $cityRepository->findOneBy(['id'=>$city])->getTown()->getId(),
            'orderBy'=>$orderBy,
            'pageIndex'=>$pageIndex,
            'pageNumbers'=>round((sizeof($list) / 12)) ,
            'towns'=>$townRepository->findAll()
            
            
        ]);
    }





    


    

    /**
     * @Route("/product-details/{id}", name="product_details_route")
     */
    public function product_details_route(ProductsRepository $productsRepository, Products $product, Request $request,ConversationRepository $conversationRepository): Response
    {

        // hundle message send
        //send-message
        $method = $request->getMethod();


        $parameters = $request->request;
        $method = $request->getMethod();

  

        $silimarProducts = $productsRepository->findBy(['category'=>$product->getCategory()->getId()],[],4);

 
        if ($method == 'POST') {  
            if ($parameters->get('send-message') != null) {
                $message = trim($parameters->get('message'));

                

                // create a chat conversation , this coneversation is unique 
                // and it will be associated for two users
                
                $sender = $this->getUser();
                $reciver = $product->getOwner();

                

                // check if first there's a conversation between connected user and product owner

                $conversation = null;

                

                if ($this->getUser() != null) {
                    
                    
                     $myConversations = $this->getUser()->getConversations();

                    // check if i'm already talking with this user about this product !!!
                    for ($i=0; $i <  sizeof($myConversations); $i++) { 
                        $conv = $myConversations[$i];

                        if ($conv->getRelatedProduct()->getId() == $product->getId()) {
                            // already exist !!!
                            $conversation = $conv;

                        }
                    }



                }


                
                
                 





                if ($conversation == null) {
                    $conversation = new Conversation();
                    $conversation->setRelatedProduct($product);
                    $conversation->addRelatedTo($sender);
                    $conversation->addRelatedTo($reciver);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($conversation);
                    $entityManager->flush();
                }



                // create the first message
                $chatMessage = new ChatMessages();
                $chatMessage->setMessage($message);
                $chatMessage->setSendDate(new DateTime());
                $chatMessage->setSender($sender);
                $chatMessage->setConversation($conversation);


                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($chatMessage);
                $entityManager->flush();

                

                return $this->redirectToRoute('product_details_route', ['id'=>$product->getId(),'messageSent'=>true], Response::HTTP_SEE_OTHER);

            }
        }

 

        return $this->render('app/product-details.html.twig', [
            'product'=>$product,
            'messageSent'=>$request->get('messageSent'),
            'silimarProducts'=>$silimarProducts
            
        ]);
    }

    

    /**
     * @Route("/account/messages/{conversation}", name="my_messages_route")
     */
    public function my_messages_route(Conversation $conversation = null,  Request $request, ConversationRepository $conversationRepository): Response
    {

 
 
        $conversations = $this->getUser()->getConversations();

         



        
        // hundle message send
        //send-message
        $method = $request->getMethod();


        $parameters = $request->request;
        $method = $request->getMethod();

 
        if ($method == 'POST') {  
            if ($parameters->get('send-message') != null) {
                $message = trim($parameters->get('message'));

                

                // create a chat conversation , this coneversation is unique 
                // and it will be associated for two users
                
                $sender = $this->getUser(); 

                 
 
                // create the first message
                $chatMessage = new ChatMessages();
                $chatMessage->setMessage($message);
                $chatMessage->setSendDate(new DateTime());
                $chatMessage->setSender($sender);
                $chatMessage->setConversation($conversation);


                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($chatMessage);
                $entityManager->flush();

                

                return $this->redirectToRoute('my_messages_route', ['conversation'=>$conversation->getId(),'messageSent'=>true], Response::HTTP_SEE_OTHER);

            }
        }

        
        

 

        return $this->render('app/my-messages.html.twig', [
            'messages'=>$conversation != null ? $conversation->getChatMessages() : [] ,
            'conversation'=>$conversation,
            'conversations'=>$conversations
        ]);
    }



    /**
     * @Route("/account/update/profile", name="edit_account_data_route", methods={"GET","POST"})
     */
    public function profile_edit_route(Request $request ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user,['isEditing'=>true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile_route', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('app/edit-profile.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }



    
    /**
     * @Route("/account/update/profile-picture-update", name="update_profile_picture", methods={"GET","POST"})
     */
    public function update_profile_picture(Request $request ): Response
    {
        $user = $this->getUser();



        $parameters = $request->request;
        $files = $request->files;
        $method = $request->getMethod();

 
        if ($method == 'POST') {  
            

            $image = $files->get('photo'); 


            // save the user
            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try { 

                    
                    $image->move('assets/img/users',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
 
                $user->setPhotoURL('/assets/img/users/'.$newFilename);
                $this->getDoctrine()->getManager()->flush();
            }
            

          
        } 
        

         
        return $this->redirectToRoute('profile_route', [], Response::HTTP_SEE_OTHER);
            

            
         

        
    }


    

    

    
 



    /**
     * @Route("/web-master", name="web_master_route")
     */
    public function web_master_route(UserRepository $userRepository, ProductsRepository $productsRepository): Response
    { 
        $members = $userRepository->findAll();
        $totalProducts = $productsRepository->findAll();
        $pendingProducts = $productsRepository->findBy(['status'=>0]);
        


   
        return $this->render('admin/index.html.twig', [
            "members"=>$members,
            "totalProducts"=>$totalProducts,
            "pendingProducts"=>$pendingProducts,
            
            
        ]);
    }



    
    /**
     * @Route("/web-master/account/product/attributes/add/{id}", name="add_product_attribute_admin_route", methods={"GET","POST"})
     */
    public function productAttributesAddAdmin(Products $product, Request $request,CategoryRepository $categoryRepository,ProductsRepository $productsRepository,AttributeValuesRepository $attributeValuesRepository): Response
    {
 
        $attributes = $product->getCategory()->getAttributeCategories();

        

        $parameters = $request->request;  

        $method = $request->getMethod();
         
        

        /**
         * "attributes-1" => "1"
         * "attributes-2" => "3"
         * "attributes-3" => "5"
         */


        if ($method == 'POST') {

            // first we delete the old ones <!DOCTYPE html>

            $oldList = $product->getProductAttributesValues();
            $entityManager = $this->getDoctrine()->getManager();
            
            

            foreach ($oldList as $cm) {
                
                $entityManager->remove($cm);
                $entityManager->flush();
            }
             
            for ($i=0; $i < sizeof($parameters) ; $i++) { 
                if ( $parameters->get('attributes-'.($i+1)) != "" ) {
                   $attributeValue = $attributeValuesRepository->findOneBy(['id'=>$parameters->get('attributes-'.($i+1))]);
                    
                   $tmp = new ProductAttributesValues();
                   $tmp ->setProduct($product);
                   $tmp->setAttribute($attributeValue);
   
                   $entityManager = $this->getDoctrine()->getManager();
                   $entityManager->persist($tmp);
                   $entityManager->flush();
   
   
                   // affect 
               }
                
            }
   
   
            return $this->redirectToRoute('products_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('app/admin-product-attributes.html.twig', [
             'product'=>$product,
             'attributes'=>$attributes
        ]);
    }

    




    /**
     * @Route("/privacy-policy", name="privacy_policy_route", methods={"GET"})
     */
    public function privacyPolicyRoute(): Response
    {
        return $this->render('app/privacy.html.twig', [ 
       ]);
    }


    /**
     * @Route("/terms-conditions", name="terms_route", methods={"GET"})
     */
    public function termsRoute(): Response
    {
        return $this->render('app/terms.html.twig', [ 
       ]);
    }


    

    /**
     * @Route("/contact-us", name="contact_us_route", methods={"GET","POST"})
     */
    public function contact_us_route(Request $request,UserRepository $userRepository, MailerInterface $mailer): Response
    {
 
        $errorMessage='';
        $successMessage='';
        

        if ($request->getMethod() == 'POST') {
            $fullname = trim($request->request->get('fullname'));
            $email = trim($request->request->get('email'));
            $subject = trim($request->request->get('subject'));
            $message = trim($request->request->get('message')); 
            $session_id = $request->request->get('captcheck_session_code');
            $answer_id = $request->request->get('captcheck_selected_answer'); 
              

            $blocEmail = '<h1>businesslink.tn</h1>';
            $blocEmail.="<h3>contact : ".$fullname."</h3>";  
            $blocEmail.="<p>".$message."</p>";                

 

            $this->getDoctrine()->getManager()->flush();


            // send verification mail
            $emailMessage = (new Email())
            ->from($email)
            ->to('support-businesslink@mydoc.tn') 
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->html($blocEmail);

            

            try {
                
                $url = 'https://captcheck.netsyms.com/api.php';
                $data = [
                    'session_id' => $session_id,
                    'answer_id' => $answer_id,
                    'action' => "verify"
                ];
                $options = [
                    'http' => [
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method' => 'POST',
                        'content' => http_build_query($data)
                    ]
                ];
                $context = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
                $resp = json_decode($result, TRUE);


                if (!$resp['result']) { 
                    $errorMessage="code de vérification est incorrect."; 
                } else { 
                    $mailer->send($emailMessage);
                    $successMessage="un e-mail de vérification a été envoyé avec succès à ".$email.", veuillez vérifier votre boîte de réception."; 
                }


            } catch (\Throwable $th) {
                 $errorMessage="Une erreur s'est produite. Veuillez réessayer."; 
            }
 
                
        
        }



        return $this->render('app/contact-us.html.twig', [ 
            'errorMessage'=>$errorMessage,
            'successMessage'=>$successMessage
       ]);
    }


    
    /**
     * @Route("/forget-password", name="forget_password_route", methods={"GET","POST"})
     */
    public function forgetPassword(Request $request,UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $errorMessage='';
        $successMessage='';
        

        if ($request->getMethod() == 'POST') {
            $email = trim($request->request->get('email'));
            $user = $userRepository->findOneBy(['email'=>$email]);
            if ($user != null) {

                $domaine = $request->server->get('HTTP_HOST');
                $token = uniqid($email,true);

                $blocEmail = '<h1>businesslink.tn</h1>
                <h3>Mot de passe oublié?</h3> 
                <p>Vous nous avez dit que vous avez oublié votre mot de passe. Définissez-en un nouveau en suivant le lien ci-dessous.</p>
                <a href="https://'.$domaine.'/new-password?token='.$token.'">réinitialiser le mot de passe</a>
                <hr/>';
                $blocEmail.="<p>Si vous n'avez pas besoin de réinitialiser votre mot de passe, ignorez simplement cet e-mail. Votre mot de passe ne changera pas.</p>";                
 

                $user->setResetPasswordToken($token);

                $this->getDoctrine()->getManager()->flush();


                // send verification mail
                $emailMessage = (new Email())
                ->from('support-businesslink@mydoc.tn')
                ->to($email) 
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Mot de passe oublié')
                ->html($blocEmail);

            

            try {
                $mailer->send($emailMessage);
                $successMessage="un e-mail de vérification a été envoyé avec succès à ".$email.", veuillez vérifier votre boîte de réception.";
             
            } catch (\Throwable $th) {
                $errorMessage="Une erreur s'est produite. Veuillez réessayer."; 
            }
 
               
            }else{
                $errorMessage= 'Mauvaise adresse e-mail, veuillez réessayer';
            }
        
        }



        return $this->render('app/forget-password.html.twig', [ 
            'errorMessage'=>$errorMessage,
            'successMessage'=>$successMessage
       ]);
    }


    

    /**
     * @Route("/new-password", name="new_password_route", methods={"GET","POST"})
     */
    public function newPassword(Request $request,UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $errorMessage='';
        $successMessage=''; 

        if ($request->getMethod() == 'POST') {

            $token = $request->query->get('token'); 
            $newPasswordTXT = trim($request->request->get('new-password'));
            $user = $userRepository->findOneBy(['resetPasswordToken'=>$token]);

             
           if ($user != null) {
              
            $user->setPassword($this->encoder->encodePassword($user,$newPasswordTXT));
            $user->setResetPasswordToken(null);

            $this->getDoctrine()->getManager()->flush();

            $successMessage ='Votre mot de passe est mis à jour avec succès.';
            
           }else{
               $errorMessage ='On dirait que vous utilisez un ancien lien.';
           }
        
        }



        return $this->render('app/new-password.html.twig', [ 
            'errorMessage'=>$errorMessage,
            'successMessage'=>$successMessage
       ]);
    }



    
    


}
