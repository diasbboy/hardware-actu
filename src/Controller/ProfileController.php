<?php 

namespace App\Controller;

use App\Form\PasswordEditType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("profile/show/{id}", name="show_profile")
     */
    public function ShowProfile(int $id,UserRepository $userRepository)
    {
        $user = $userRepository->find($id);

        // Si l'utilisateur n'est pas connecté , le renvoyer vers la page d'accueil
        if($user !== $this->getUser())
        {
            //Ajouter message flash
            return $this->redirectToRoute('home');
        }
        return $this->render("profile/show.html.twig",[
            'user' => $user,
        ]);
    }


    /**
     * @Route("profile/{id}", name="user_profile")
     */
    public function profile(int $id,UserRepository $userRepository,Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $userConnected = $userRepository->find($id);

        if($userConnected !== $this->getUser())
        {
            //Ajouter message flash
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(PasswordEditType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $userConnected->setPassword(
                $passwordEncoder->encodePassword(
                    $userConnected,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render("profile/editPassword.html.twig",[
            'formPassword' =>  $form->createView()
        ]);
    }
}