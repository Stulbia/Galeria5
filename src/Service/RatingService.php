<?php

/**
 * Rating service.
 */

namespace App\Service;

use App\Entity\Rating;
use App\Entity\Photo;
use App\Entity\User;
use App\Repository\RatingRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class RatingService.
 */
class RatingService implements RatingServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param RatingRepository   $ratingRepository Rating repository
     * @param PaginatorInterface $paginator        Paginator
     */
    public function __construct(private readonly RatingRepository $ratingRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list by Photo.
     *
     * @param Photo $photo Photo
     * @param int   $page  Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function findByPhoto(Photo $photo, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->ratingRepository->findByPhoto($photo),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

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
    public function findAverageRatingByPhoto(Photo $photo): ?float
    {
        $ave = $this->ratingRepository->findAverageRatingByPhoto($photo) ?? 0;

        return $ave;
    }

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
    public function save(Rating $rating, UserInterface $user, Photo $photo): void
    {
        $rating->setUser($user);
        $rating->setPhoto($photo);

        $this->ratingRepository->save($rating);
    }

    /** Delete entity.
     *
     * @param Rating $rating Rating entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rating $rating): void
    {
        $this->ratingRepository->delete($rating);
    }

    /**
     * Select Ratings by User and Photo.
     *
     * @param User  $user  User
     * @param Photo $photo Photo
     *
     * @return Rating|null rating
     */
    public function findByUserAndPhoto(User $user, Photo $photo): ?Rating
    {
        return $this->ratingRepository->findByUserAndPhoto($user, $photo);
    }

    /**
     * Find order of certain photos.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function findPhotoOrder(int $page = 1): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->ratingRepository->findPhotoOrder(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }
}
