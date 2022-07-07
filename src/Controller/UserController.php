<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/web-master/user")
 */
class UserController extends AbstractController
{

    private $encoder;
 
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }



    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $error = '';
        if ($form->isSubmitted() && $form->isValid()) {
            

            


            // check if email already in use
            if ($userRepository->findOneBy(['email' => $user->getEmail()]) != null) {
                $error = "Cette adresse e-mail est déjà utilisée par un autre utilisateur";
            } else {
                $user->setRoles(['ROLE_CLIENT']);
                $user->setPhotoURL('/assets/img/users/avatar.png');


                $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
                $user->setIsBlocked(false);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
            }



            
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'error'=>$error
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user,['isEditing'=>true,'adminEditing'=>true]);
        $form->handleRequest($request);
        $error='';


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }



        // password update form for the admin
        
        $method = $request->getMethod(); 

        if ($method == 'POST') {
            $params = $request->request;

            if ($params->get('update-password')) { 

                $newPassword = $params->get('new-password');
 
 
                $user = $this->getUser();
                $user->setPassword($this->encoder->encodePassword($this->getUser(),$newPassword)  );
                
                $this->getDoctrine()->getManager()->flush(); 
            }

        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'error'=>$error
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }





    public function profile_edit_route(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
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
}
