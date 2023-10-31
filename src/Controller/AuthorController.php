<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/author')]

class AuthorController extends AbstractController
{
    public function __construct(ManagerRegistry $mr)
    {
        
    }
    #[Route('/', name: 'app_author')]
    public function index(AuthorRepository $authorRepository ): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $authorRepository->findAll(),
        ]);
    }
    #[Route('/show', name: 'showauth', methods: ['GET'])]
  public function fetchStudent(AuthorRepository $repo,ManagerRegistry $mr ){
//$authors=$repo->findAll();
$authors=$mr->getRepository(Author::class);
return $this->render('author/show.html.twig',[
    'authors'=>$repo->findAll()
]);
    }


    #[Route('/new', name: 'app_author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $mr): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em=$mr->getManager();

        $em->persist($author);
        $em->flush();
        }
        return $this->renderForm('author/new.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }
      
    #[Route("/{id}", name: "auth_delete",methods: ['POST'])]
    
    public function delete(Author $auth,ManagerRegistry $mr): Response
    {
        // Check if the computer exists
        if (!$auth) {
            throw $this->createNotFoundException('auth not found');
        }
        // Remove the computer from the database
        
        $em = $mr->getManager();

        $em->remove($auth);
        $em->flush();

        // Optionally, redirect to a success page or return a response
        return $this->redirectToRoute('email'); // Replace with your route
    }
  
    #[Route("/edit/{id}", name: "edit_auth")]
public function editAuthor(Author $author, ManagerRegistry $mr , Request $request): Response

{
    $form = $this->createForm(AuthorType::class, $author);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persist the changes to the database
        $em=$mr->getManager();

        $em->persist($author);
        $em->flush();

        // Redirect to a confirmation page or the list of authors
        return $this->redirectToRoute('list_authors');
    }

    return $this->render('author/edit.html.twig', [
        'form' => $form->createView(),
        'author' => $author, // Pass the author object to the template

    ]);}


#[Route("/bookzeroo", name: "book_zero")]

public function deletenbbookszero(AuthorRepository $rep)
{
    $authorsWithZeroNbBooks = $rep->findBy(['nbbooks' => 0], null, null);
    $entityManager = $this->getDoctrine()->getManager();

    foreach ($authorsWithZeroNbBooks as $author) {
        $entityManager->remove($author);
    }

    $entityManager->flush();
    return $this->redirectToRoute('showauth'); }




    }

