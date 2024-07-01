<?php

/**
 * Rating repository.
 */

namespace App\Repository;

use App\Entity\Rating;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PhotoRepository.
 *
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[]    findAll()
 * @method Rating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Photo>
 */
class RatingRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial rating.{id, value}',
                ' photo',
                'partial user.{id}',
            )
            ->join('rating.photo', 'photo')
            ->orderBy('rating.value', 'DESC');
    }

    /**
     * Select Ratings by Photo.
     *
     * @param Photo $photo Photo
     *
     * @return QueryBuilder Query builder
     */
    public function findByPhoto(Photo $photo): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('partial rating.{id, value}')
            ->where('rating.photo = :photo')
            ->setParameter('photo', $photo);
    }

    /**
     * Select Average Rating by Photo.
     *
     * @param Photo $photo Photo
     *
     * @return float|null Average rating or null if no ratings
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findAverageRatingByPhoto(Photo $photo): ?float
    {
        $result = $this->getOrCreateQueryBuilder()
            ->select('AVG(rating.value) as avgRating')
            ->where('rating.photo = :photo')
            ->setParameter('photo', $photo)
            ->getQuery()
            ->getSingleScalarResult();

        return null !== $result ? (float) $result : null;
    }

    /**
     * Find order of certain photos.
     *
     * @return QueryBuilder Query Builder
     */
    public function findPhotoOrder(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('AVG(rating.value) as avg_value, photo.id, photo.filename, photo.title')
            ->join('rating.photo', 'photo')
            ->orderBy('avg_value', 'DESC')
            ->groupBy('photo');
    }

    /**
     * Select Ratings by User and Photo.
     *
     * @param User  $user  User
     * @param Photo $photo Photo
     *
     * @return Rating|null Query builder
     */
    public function findByUserAndPhoto(User $user, Photo $photo): ?Rating
    {
        $queryBuilder = $this->createQueryBuilder('rating')
            ->select('partial rating.{ id, value }')
            ->where('rating.user = :user')
            ->andWhere('rating.photo = :photo')
            ->setParameter('user', $user)
            ->setParameter('photo', $photo);

        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException|NonUniqueResultException) {
            return null;
        }
    }

    /**
     * Save entity.
     *
     * @param Rating $rating Photo entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rating $rating): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($rating);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Rating $rating Photo entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rating $rating): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($rating);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('rating');
    }

    //    /**
    //     * @return Rating[] Returns an array of Rating objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rating
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
