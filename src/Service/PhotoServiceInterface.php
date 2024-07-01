<?php

/**
 * Photo service interface.
 */

namespace App\Service;

use App\Dto\PhotoListInputFiltersDto;
use App\Dto\PhotoSearchInputFiltersDto;
use App\Entity\Photo;
use App\Entity\Tag;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface PhotoServiceInterface.
 */
interface PhotoServiceInterface
{
    /**
     * Get paginated list for all photos.
     *
     * @param int                      $page    Page number
     * @param PhotoListInputFiltersDto $filters Filter
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, PhotoListInputFiltersDto $filters): PaginationInterface;

    /**
     * Get paginated list for searched photos.
     *
     * @param int                        $page    Page number
     * @param PhotoSearchInputFiltersDto $filters Filter
     *
     * @return PaginationInterface<string, mixed> Paginated list
     *
     * @throws NonUniqueResultException
     */
    public function getSearchList(int $page, PhotoSearchInputFiltersDto $filters): PaginationInterface;

    /**
     * Get paginated list.
     *
     * @param int                      $page    Page number
     * @param UserInterface            $author  author
     * @param PhotoListInputFiltersDto $filters Filter
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedUserList(int $page, UserInterface $author, PhotoListInputFiltersDto $filters): PaginationInterface;

    /**
     * Save photo.
     *
     * @param Photo         $photo        Photo entity
     * @param UploadedFile  $uploadedFile Uploaded file
     * @param UserInterface $user         User entity
     */
    public function save(Photo $photo, UploadedFile $uploadedFile, UserInterface $user): void;

    /**
     * Update photo.
     *
     * @param Photo $photo Photo entity
     */
    public function edit(Photo $photo): void;

    /**
     * Delete entity.
     *
     * @param Photo $photo Photo entity
     */
    public function delete(Photo $photo): void;

    /**
     * Find Photos by Tag Name.
     *
     * @param Tag[] $tagName Tag Name
     *
     * @return Photo[]
     */
    public function findByTags(array $tagName): array;
}
