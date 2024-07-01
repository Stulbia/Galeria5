<?php

/**
 * Comment service.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CommentService.
 */
class CommentService implements CommentServiceInterface
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
     * @param CommentRepository  $commentRepository Comment repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(private readonly CommentRepository $commentRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->commentRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
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
            $this->commentRepository->findByPhoto($photo),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list by Photo.
     *
     * @param User $user User
     * @param int  $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function findByUser(User $user, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->commentRepository->findByUser($user),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

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
    public function save(Comment $comment, UserInterface $user, Photo $photo): void
    {
        $comment->setUser($user);
        $comment->setPhoto($photo);

        $this->commentRepository->save($comment);
    }

    /** Delete entity.
     *
     * @param Comment $comment Comment entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }
}
