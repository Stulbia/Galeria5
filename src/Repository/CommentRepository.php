<?php

/**
 * Comment repository.
 */

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PhotoRepository.
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Photo>
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
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
                'partial comment.{photo, id, createdAt, updatedAt, content, user}',
                'partial photo.{id, title}',
                'partial user.{id, email, name}',
            )
            ->join('comment.photo', 'photo')
            ->orderBy('comment.updatedAt', 'DESC');
    }

    /**
     * Select Comments by Photo.
     *
     * @param Photo $photo Photo
     *
     * @return QueryBuilder Query builder
     */
    public function findByPhoto(Photo $photo): QueryBuilder
    {
        return $this->createQueryBuilder('comment')
            ->select('partial comment.{id, createdAt, updatedAt, content}')
            ->where('comment.photo = :photo')
            ->setParameter('photo', $photo);
    }

    /**
     * Select Comments by User.
     *
     * @param User $user User
     *
     * @return QueryBuilder Query builder
     */
    public function findByUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('comments')
            ->select('partial comment.{ id, user, createdAt, updatedAt, content, photo}')
            ->where('comment.user = :user')
            ->setParameter('user', $user);
    }

    /**
     * Save entity.
     *
     * @param Comment $comment Photo entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Comment $comment): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($comment);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment Photo entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Comment $comment): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($comment);
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
        return $queryBuilder ?? $this->createQueryBuilder('comment');
    }

    //    /**
    //     * @return Comment[] Returns an array of Comment objects
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

    //    public function findOneBySomeField($value): ?Comment
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
