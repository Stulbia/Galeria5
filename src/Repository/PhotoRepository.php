<?php

/**
 * Photo repository.
 */

namespace App\Repository;

use App\Dto\PhotoListFiltersDto;
use App\Dto\PhotoSearchFiltersDto;
use App\Entity\Enum\PhotoStatus;
use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
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
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Photo>
 */
class PhotoRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * Query all records.
     *
     * @param PhotoListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(PhotoListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial photo.{id, createdAt, updatedAt, title, description, filename}',
                'partial gallery.{id, title}',
                'partial tags.{id, title}'
            )
            ->join('photo.gallery', 'gallery')
            ->leftJoin('photo.tags', 'tags')
            ->orderBy('photo.updatedAt', 'DESC');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * Query searched records.
     *
     * @param PhotoSearchFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function querySearch(PhotoSearchFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial photo.{id, createdAt, updatedAt, title, description, filename}',
                'partial gallery.{id, title}',
                'partial tags.{id, title}'
            )
            ->join('photo.gallery', 'gallery')
            ->leftJoin('photo.tags', 'tags')
            ->orderBy('photo.updatedAt', 'DESC');

        return $this->applyFiltersToSearchList($queryBuilder, $filters);
    }

    /**
     * Count photos by gallery.
     *
     * @param Gallery $gallery Gallery
     *
     * @return int Number of photos in gallery
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByGallery(Gallery $gallery): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('photo.id'))
            ->where('photo.gallery = :gallery')
            ->setParameter(':gallery', $gallery)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // ...
    /**
     * Query photo by author.
     *
     * @param User                $user    User entity
     * @param PhotoListFiltersDto $filters Filter
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(User $user, PhotoListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);

        $queryBuilder->andWhere('photo.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }
    //    /**
    //     * Select photos by Tags.
    //     *
    //     * @param Gallery $gallery Gallery
    //     *
    //     * @return QueryBuilder Query builder
    //     *
    //     * @throws NoResultException
    //     */
    //    public function findByTag($tag):QueryBuilder
    //    {
    //        return $this->createQueryBuilder('photo')
    //            ->select('partial photo.{id, createdAt, updatedAt, title}')
    //            ->join('photo.tags', 'tag')
    //            ->where('tag.id = :tag')
    //            ->setParameter('tag', $tag);
    //    }

    /**
     * Save entity.
     *
     * @param Photo $photo Photo entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Photo $photo): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($photo);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Photo $photo Photo entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Photo $photo): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($photo);
        $this->_em->flush();
    }

    /**
     * Find by Tags.
     *
     * @param Tag[] $tags
     *
     * @return Photo[]
     */
    public function findByTags(array $tags): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->distinct()
            ->innerJoin('p.tags', 't')
            ->andWhere('t IN (:tags)')
            ->setParameter('tags', $tags);

        return $queryBuilder->getQuery()->getResult();
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
        return $queryBuilder ?? $this->createQueryBuilder('photo');
    }

    /**
     * Apply filters to paginated list.
     *
     * @param QueryBuilder        $queryBuilder Query builder
     * @param PhotoListFiltersDto $filters      Filters
     *
     * @return QueryBuilder Query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, PhotoListFiltersDto $filters): QueryBuilder
    {
        if ($filters->gallery instanceof Gallery) {
            $queryBuilder->andWhere('gallery = :gallery')
                ->setParameter('gallery', $filters->gallery);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters->tag);
        }

        if ($filters->photoStatus instanceof PhotoStatus) {
            $queryBuilder->andWhere('photo.status = :status')
                ->setParameter('status', $filters->photoStatus->value, Types::STRING);
        }

        return $queryBuilder;
    }

    /**
     * Apply filters to paginated list.
     *
     * @param QueryBuilder          $queryBuilder Query builder
     * @param PhotoSearchFiltersDto $filters      Filters
     *
     * @return QueryBuilder Query builder
     */
    private function applyFiltersToSearchList(QueryBuilder $queryBuilder, PhotoSearchFiltersDto $filters): QueryBuilder
    {
        if ($filters->gallery instanceof Gallery) {
            $queryBuilder->andWhere('gallery = :gallery')
                ->setParameter('gallery', $filters->gallery);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters->tag);
        }

        if ($filters->photoStatus instanceof PhotoStatus) {
            $queryBuilder->andWhere('photo.status = :status')
                ->setParameter('status', $filters->photoStatus->value, Types::STRING);
        }

        if (null !== $filters->titlePattern) {
            $queryBuilder->andWhere('photo.title LIKE :titlePattern')
                ->setParameter('titlePattern', '%'.$filters->titlePattern.'%');
        }

        if (null !== $filters->descriptionPattern) {
            $queryBuilder->andWhere('photo.description LIKE :descriptionPattern')
                ->setParameter('descriptionPattern', '%'.$filters->descriptionPattern.'%');
        }

        return $queryBuilder;
    }
}
