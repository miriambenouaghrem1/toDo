<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\BookType;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/book')]


class BooksController extends AbstractController

{  
    private EntityManagerInterface $entityManager;  // Inject the EntityManager

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/books', name: 'app_books')]
    public function index(): Response
    {
        return $this->render('books/index.html.twig', [
            'controller_name' => 'BooksController',
        ]);
    }
    #[Route("/list", name: "book_list")]
    public function list(Request $request, BookRepository $rep): Response {
        $searchTerm = $request->query->get('search');
        $result = $rep->findBySearchTerm($searchTerm);
    
        return $this->render('books/list.html.twig', [
            'search' => $searchTerm, // Pass the search term as a variable
            'book' => $result,
        ]);
    }
    
    #[Route('/show', name: 'show')]
    public function fetchStudent(BookRepository $repo,ManagerRegistry $mr ){
//$authors=$repo->findAll();
$books=$mr->getRepository(Book::class);
return $this->render('books/show.html.twig',[
    'b'=>$books->findAll()
]);
    }
    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,AuthorRepository $repauth): Response
    {
        
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        
        $authorBooks = $form->get('Authors')->getData();
        $auth=$repauth->findOneById($authorBooks);
        if ($authorBooks !== null) {
        $res=$auth->getNbbooks(); 
        $auth->setNbbooks($res+1); 
        }
        if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($book);
        $entityManager->flush();
        }
        return $this->renderForm('books/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
        return $this->redirectToRoute('show'); // Replace with your route

    }
      
    #[Route("/{id}", name: "book_delete",methods: ['POST'])]
    
    public function delete(Book $book,ManagerRegistry $mr): Response
    {
        // Check if the computer exists
        if (!$book) {
            throw $this->createNotFoundException('book not found');
        }
        // Remove the computer from the database
        
        $em = $mr->getManager();

        $em->remove($book);
        $em->flush();

        // Optionally, redirect to a success page or return a response
        return $this->redirectToRoute('show'); // Replace with your route
    }
    #[Route("/published", name: "bookPublished")]

public function findPublished(BookRepository $repo){
    $booksP=$repo->countPublished('yes');
    $booksNP=$repo->countPublished('no');

    $bookspub=$repo->findPublished('yes');

    return $this->render('books/published.html.twig', [
        'bookspub'=>$bookspub,
        'booksP'=>$booksP,
        'booksNP'=>$booksNP ]);
}
#[Route('/edit/{id}', name: 'edit_book')]
public function editBook(Request $request, int $id): Response
{
    $book = $this->entityManager->getRepository(Book::class)->find($id);

    if (!$book) {
        throw $this->createNotFoundException('Book not found');
    }

    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->entityManager->flush();

        return $this->redirectToRoute('show'); // Replace with your list route
    }

    return $this->render('books/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
#[Route("/detail/{id}", name: "detail_book")]

public function details(Book $book, EntityManagerInterface $em, Request $request){
    {
        if (!$book) {
            throw $this->createNotFoundException('Author not found');
        }
    
        // Create a form for editing the author
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the new number of books from the form
    
            // Use Query Builder to update the number of books for the specific author
            $qb = $em->createQueryBuilder();
           
    
            return $this->redirectToRoute('show'); // Redirect to the show page or another appropriate route
        }
        return $this->render('books/detail.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }
}


}