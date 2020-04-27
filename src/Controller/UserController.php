<?php

namespace App\Controller;

use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\Categorie;
use App\Entity\Question;
use App\Repository\ReponseRepository;

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
        $repo = $this->getDoctrine()->getRepository(Categorie::class);
        $categorieQuiz = $repo->findAll();

        return $this->render('user/home.html.twig', [
            'categorie' => $categorieQuiz
        ]);
    }

    /**
     * @Route("/categorie/{id}/{ques}", name="categorie")
     */
    public function showByCategorieId($id, $ques, ReponseRepository $repos)
    {
        if (!$ques) {
            $ques = 1;
        }

        $repo = $this->getDoctrine()->getRepository(Question::class);
        $questionByCategorie = $repo->findBy([
                'id_categorie' => $id
            ]);

        $callRepo = $repos->findBy([
            'id_question' => $ques
        ]);

        $reponseExpected = $repos->findBy([
            'id_question' => $ques,
            'reponse_expected' => 1
        ]);

        if (isset($_POST['reponse'])) {
            return $this->render('user/categorieId.html.twig', [
                'question' => $questionByCategorie,
                'reponse' => $callRepo,
                'ques' => $ques,
                'post' => $_POST['reponse'],
                'valide' => $reponseExpected
            ]);
        }
        else {
            return $this->render('user/categorieId.html.twig', [
                'question' => $questionByCategorie,
                'reponse' => $callRepo,
                'ques' => $ques,
                'valide' => $reponseExpected
                ]);
        }
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
        
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function editPorfile(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            
            $this->getUser()->setPassword($hash);
            $this->getUser()->setUsername($user->getUsername());
            $this->getUser()->setEmail($user->getEmail());
        
            $manager->persist($this->getUser());
            $manager->flush();
        }

        if ($this->getUser()) {
            return $this->render('user/profile.html.twig', [
                'form' => $form->createView()
                ]);
        }
        else {
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/login", name="login")
     */
    public function login() 
    {
        return $this->render('user/login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() 
    {
        
    }
}
