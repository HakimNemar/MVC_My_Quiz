<?php

namespace App\Controller;

use App\Form\RegistrationType;
use App\Form\CreateQuizType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Repository\ReponseRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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
    public function registerUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
        
            $manager->persist($user);
            $manager->flush();

            $email = (new Email())
            ->from("hakim.nemar@epitech.eu")
            ->to($user->getEmail())
            ->text("Account created !");

            $mailer->send($email);

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
     * @Route("/createQuiz", name="create.quiz")
     */
    public function createQuiz(Request $request, EntityManagerInterface $manager)
    {
        $categorie = new Categorie();
        $question = new Question();
        $reponse = new Reponse();
        $reponse2 = new Reponse();
        $reponse3 = new Reponse();

        $form = $this->createForm(CreateQuizType::class, [$categorie, $question, $reponse]);

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $repo = $this->getDoctrine()->getRepository(Categorie::class);
            $idCategorie = $repo->findOneBy([
                    'name' => $_POST['create_quiz']['Categorie']
                ]);
            
            $question->setQuestion($_POST['create_quiz']['Question'])
                     ->setIdCategorie($idCategorie->getId())
                     ->setCategorie($idCategorie);
            $manager->persist($question);
            $manager->flush();

            $reponse->setIdQuestion($question->getId())
                    ->setReponse($_POST['create_quiz']['ReponseCorrect'])
                    ->setReponseExpected(true)
                    ->setQuestions($question);
            $manager->persist($reponse);
            $manager->flush();
            
            $reponse2->setIdQuestion($question->getId())
                    ->setReponse($_POST['create_quiz']['ReponseFausse_1'])
                    ->setReponseExpected(false)
                    ->setQuestions($question);
            $manager->persist($reponse2);
            $manager->flush();
            
            $reponse3->setIdQuestion($question->getId())
                    ->setReponse($_POST['create_quiz']['ReponseFausse_2'])
                    ->setReponseExpected(false)
                    ->setQuestions($question);
            $manager->persist($reponse3);
            $manager->flush();
        }

        if ($this->getUser()) {
            return $this->render("user/createQuiz.html.twig", [
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
