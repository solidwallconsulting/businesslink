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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class SecurityController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }



    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('profile_route');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $errorMessage=''; 
        
        if ($request->query->get('message')) {
            $errorMessage=$request->query->get('message');
        }


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'errorMessage' => $errorMessage]);
    }





    /**
     * @Route("/email-verification", name="email_verification", methods={"GET","POST"})
     */
    public function email_verification(MailerInterface $mailer, Request $request, UserRepository $userRepository): Response
    {

        $email = $request->query->get('email');


        return $this->render('security/email-verification.html.twig', [
            "email" => $email
        ]);
    }

    


    /**
     * @Route("/validate-account", name="validate_account", methods={"GET","POST"})
     */
    public function validate_account(MailerInterface $mailer, Request $request, UserRepository $userRepository): Response
    {

        $token = $request->query->get('token');
        $user = $userRepository->findOneBy(['validationToken'=>$token]);
        $error = "";
        $success = "";
        

        if ($user) {
            $success = 'Votre compte est activé avec succès, <br/>Vous pouvez vous <a href="/login" >connecter maintenant</a>';
            $user->setValidationToken(null);
            $user->setIsActive(true);
            $this->getDoctrine()->getManager()->flush();


        }else{
             $error='Lien non trouvé, on dirait que vous utilisez un ancien lien.';
        }


        return $this->render('security/success-validation.html.twig', [
            "error" => $error,
            "success" => $success,
            
        ]);
    }


    /**
     * @Route("/signup", name="app_siginup", methods={"GET","POST"})
     */
    public function new(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $error = "";



        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // check if email already in use
            if ($userRepository->findOneBy(['email' => $user->getEmail()]) != null) {
                $error = "Cette adresse e-mail est déjà utilisée par un autre utilisateur";
            } else {
                $validationToken = uniqid($user->getEmail(), true);

                $user->setRoles(['ROLE_CLIENT']);
                $user->setPhotoURL('/assets/img/users/avatar.png');
                $user->setIsBlocked(false);
                $user->setIsActive(false);
                $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
                $user->setValidationToken($validationToken);


                $email = $user->getEmail();
                $domaine = $request->server->get('HTTP_HOST');




                $blocEmail = '<h1>businesslink.tn</h1>
        <h3>Validation du compte</h3> 
        <p>Merci de vous être inscrit sur www.businesslink.tn</p>
        <a href="https://' . $domaine . '/validate-account?token=' . $validationToken . '">Veuillez utiliser ce lien pour terminer votre inscription.</a>
        <hr/>';
                $blocEmail .= "www.businesslink.tn";

                $this->getDoctrine()->getManager()->flush();


                // send verification mail
                $emailMessage = (new Email())
                    ->from('support-businesslink@mydoc.tn')
                    ->to($email)
                    ->priority(Email::PRIORITY_HIGH)
                    ->subject('Validation du compte')
                    ->html($blocEmail);



                try {
                    $mailer->send($emailMessage);
                    
                    $error = "un e-mail de vérification a été envoyé avec succès à " . $email . ", veuillez vérifier votre boîte de réception.";

                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $this->redirectToRoute('email_verification', ["email" => $user->getEmail()]);


                } catch (\Throwable $th) {
                    dump($th);
                    $error = "Une erreur s'est produite. Veuillez réessayer.";
                }
            }
        } 
        return $this->render('security/signup.html.twig', [

            'form' => $form->createView(),
            'error' => $error
        ]);
    }



    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
