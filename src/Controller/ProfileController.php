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

        return $this->render("profile/detail.html.twig",[
            'formPassword' =>  $form->createView()
        ]);
    }
}