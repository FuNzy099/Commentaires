<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Opinion;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Opinion>
 *
 * @method Opinion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Opinion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Opinion[]    findAll()
 * @method Opinion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpinionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Opinion::class);
        $this -> paginator = $paginator;
    }

    public function add(Opinion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Opinion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return PaginationInterface
     */
    public function search(SearchData $search, $slug): PaginationInterface{

        $query = $this
            ->createQueryBuilder('opinion')
            ->innerJoin('opinion.product', 'product')
            ->andwhere('product.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('opinion.createDate', 'DESC');

            if (!empty($search->q)) {
                $query = $query
                    ->andWhere('opinion.pseudonyme LIKE :q')
                    ->setParameter('q', "%{$search->q}%");
            }
    

        if (!empty($search->min)) {
            $query = $query
                ->andWhere('opinion.note >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max)) {
            $query = $query
                ->andWhere('opinion.note <= :max')
                ->setParameter('max', $search->max);
        }

        $query = $query -> getQuery();

        return $this -> paginator -> paginate(
            $query,
            $search -> page,
            3
        );
    }

//    /**
//     * @return Opinion[] Returns an array of Opinion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Opinion
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
