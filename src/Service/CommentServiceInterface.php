<?php

/**
 * Comment service interface.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

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
     * @param Comment $comment Comment entity
     * @param User    $user    User entity
     * @param Photo   $photo   Photo
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Comment $comment, UserInterface $user, Photo $photo): void;

    /** Delete entity.
     *
     * @param Comment $comment Comment entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Comment $comment): void;

    /**
     * Get paginated list by Photo.
     *
     * @param User $user User
     * @param int  $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function findByUser(User $user, int $page): PaginationInterface;
}
