<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

//    /**
//     * @return Author[] Returns an array of Author objects
//     */
   public function findByExampleField($value): array
  {
      return $this->createQueryBuilder('a')
          ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
              ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
           ->getQuery()
            ->getResult()
        ;
    }

   public function findOneBySomeField($value): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
public function listAuthorByEmail(){
    return $this->createQueryBuilder('a')
    ->orderBy('a.email')
    ->getQuery()
    ->getResult()
    ;
}
// public function editAuthor(Author $a){
//     $query = $entityManager
//         ->createQueryBuilder()
//         ->select('b') // Select the Book entity
//         ->from('App\Entity\Book', 'b')
//         ->
// }
public function findOneById($id): ?Author
{
    return $this->createQueryBuilder('a')
        ->andWhere('a.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
}
public function minMax($minValue, $maxValue) {
    $dql = "SELECT a FROM App\Entity\Author a
            WHERE a.nbbooks BETWEEN :minValue AND :maxValue";

    return $this->getEntityManager()
        ->createQuery($dql)
        ->setParameter('minValue', $minValue)
        ->setParameter('maxValue', $maxValue)
        ->getResult();
}
public function authbook(EntityManagerInterface $entityManager)
{
    // Select authors with zero books
    $dql = "SELECT a FROM App\Entity\Author a WHERE a.nbbooks = 0";
    $query = $entityManager->createQuery($dql);
    $authorsToDelete = $query->getResult();

    // Delete authors with zero books
    foreach ($authorsToDelete as $author) {
        $entityManager->remove($author);
    }

    $entityManager->flush();

    return count($authorsToDelete); // Return the number of deleted authors
}
}
