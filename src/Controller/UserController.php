<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('user/home.html.twig');
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerUser(Request $request, EntityManagerInterface $manager)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
                     ->add('email')
                     ->add('password', PasswordType::class)
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
        }
        
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login() 
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
                     ->add('email')
                     ->add('password', PasswordType::class)
                     ->getForm();

        return $this->render('user/login.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
