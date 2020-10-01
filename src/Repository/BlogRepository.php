<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    public function getAllQuery()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->getQuery();
    }

    public function getAllQueryBuilder()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC');
    }

    public function querySearchForIndex(?string $q = null, ?int $tagId = null)
    {
        /** @var QueryBuilder $query */
        $query = $this->getAllQueryBuilder()
            ->innerJoin('b.user', 'user');

        if ($q) {
            $query
                ->andWhere(
                    $query->expr()->like("b.title", ":title")
                )
                ->setParameter(':title', '%'.mb_strtolower($q).'%')
                ->orWhere(
                    $query->expr()->like("b.description", ":description")
                )
                ->setParameter(':description', '%'.mb_strtolower($q).'%');
        }

        if ($tagId) {
            $query
                ->innerJoin('b.tags', 'tags')
                ->andWhere('tags.id = :tagId')
                ->setParameter(':tagId', $tagId);
        }

        return $query;
    }

    /**
     * @return Blog[] Returns an array of Blog objects
     */
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySomeField($value): ?Blog
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
