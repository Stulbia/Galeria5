<?php

/**
 * Rating service interface.
 */

namespace App\Service;

use App\Entity\Rating;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface RatingServiceInterface.
 */
interface RatingServiceInterface
{
    /**
     * Get paginated list by Photo.
     *
     * @param Photo $photo Photo
     * @param int   $page  Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function findByPhoto(Photo $photo, int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Rating $rating Rating entity
     * @param User   $user   User entity
     * @param Photo  $photo  Photo
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rating $rating, UserInterface $user, Photo $photo): void;

    /** Delete entity.
     *
     * @param Rating $rating Rating entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rating $rating): void;

    /**
     * Select Ratings by User and Photo.
     *
     * @param User  $user  User
     * @param Photo $photo Photo
     *
     * @return Rating|null rating
     */
    public function findByUserAndPhoto(User $user, Photo $photo): ?Rating;

    /**
     * Get average rating on Photo.
     *
     * @param Photo $photo Photo
     *
     * @return float|null Average Rating
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findAverageRatingByPhoto(Photo $photo): ?float;

    /**
     * Find order of certain photos.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function findPhotoOrder(int $page): PaginationInterface;
}
