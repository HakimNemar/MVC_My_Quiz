<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
    public function register() 
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
                     ->add('email', TextType::class, [ 
                         'attr' => [ 
                             'placeholder' => 'exemple@exemple.fr'
                         ]
                     ])
                     ->add('password', PasswordType::class, [ 
                        'attr' => [ 
                            'placeholder' => '•••••'
                        ]
                    ])
                     ->getForm();

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
