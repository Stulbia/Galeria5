<?php

/**
 * Photo service.
 */

namespace App\Service;

use App\Dto\PhotoListFiltersDto;
use App\Dto\PhotoListInputFiltersDto;
use App\Dto\PhotoSearchFiltersDto;
use App\Dto\PhotoSearchInputFiltersDto;
use App\Entity\Enum\PhotoStatus;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\PhotoRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PhotoService.
 */
class PhotoService implements PhotoServiceInterface
{
    /**
     * Constructor.
     *
     * @param string                     $targetDirectory   Target directory
     * @param PhotoRepository            $photoRepository   Photo repository
     * @param FileUploadServiceInterface $fileUploadService File upload service
     * @param Filesystem                 $filesystem        Filesystem component
     * @param PaginatorInterface         $paginator         Paginator
     * @param TagServiceInterface        $tagService        Tag service
     * @param GalleryServiceInterface    $galleryService    Gallery service
     */
    public function __construct(private readonly string $targetDirectory, private readonly PhotoRepository $photoRepository, private readonly FileUploadServiceInterface $fileUploadService, private readonly Filesystem $filesystem, private readonly PaginatorInterface $paginator, private readonly TagServiceInterface $tagService, private readonly GalleryServiceInterface $galleryService)
    {
    }

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
     * Get paginated list for user photos.
     *
     * @param int                      $page    Page number
     * @param User                     $author  Photo author
     * @param PhotoListInputFiltersDto $filters Filter
     *
     * @return PaginationInterface<string, mixed> Paginated list
     *
     * @throws NonUniqueResultException
     */
    public function getPaginatedUserList(int $page, UserInterface $author, PhotoListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->photoRepository->queryByAuthor($author, $filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list for all photos.
     *
     * @param int                      $page    Page number
     * @param PhotoListInputFiltersDto $filters Filter
     *
     * @return PaginationInterface<string, mixed> Paginated list
     *
     * @throws NonUniqueResultException
     */
    public function getPaginatedList(int $page, PhotoListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->photoRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

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
    public function getSearchList(int $page, PhotoSearchInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareSearchFilters($filters);

        return $this->paginator->paginate(
            $this->photoRepository->querySearch($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save photo.
     *
     * @param Photo         $photo        Photo entity
     * @param UploadedFile  $uploadedFile Uploaded file
     * @param UserInterface $user         User entity
     */
    public function save(Photo $photo, UploadedFile $uploadedFile, UserInterface $user): void
    {
        $photoFilename = $this->fileUploadService->upload($uploadedFile);
        $photo->setAuthor($user);
        $photo->setFilename($photoFilename);
        try {
            $this->photoRepository->save($photo);
        } catch (OptimisticLockException|ORMException) {
        }
    }

    /**
     * Update photo.
     *
     * @param Photo $photo Photo entity
     */
    public function edit(Photo $photo): void
    {
        try {
            $this->photoRepository->save($photo);
        } catch (OptimisticLockException|ORMException) {
        }
    }

    /**
     * Delete photo.
     *
     * @param Photo $photo Photo entity
     *
     * @throws ORMException             if an ORM error occurs
     * @throws OptimisticLockException  if a version conflict occurs
     * @throws InvalidArgumentException if the provided tag is invalid
     */
    public function delete(Photo $photo): void
    {
        $filename = $photo->getFilename();
        if (null !== $filename) {
            $this->filesystem->remove($this->targetDirectory.'/'.$filename);
        }
        $this->photoRepository->delete($photo);
    }

    /**
     * Find Photos by Tag Name.
     *
     * @param Tag[] $tagName Tag Name
     *
     * @return Photo[]
     */
    public function findByTags(array $tagName): array
    {
        return $this->photoRepository->findByTags($tagName);
    }

    /**
     * Prepare filters for the photos list.
     *
     * @param PhotoListInputFiltersDto $filters Raw filters from request
     *
     * @return PhotoListFiltersDto Result filters
     *
     * @throws NonUniqueResultException
     */
    private function prepareFilters(PhotoListInputFiltersDto $filters): PhotoListFiltersDto
    {
        return new PhotoListFiltersDto(
            null !== $filters->galleryId ? $this->galleryService->findOneById($filters->galleryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
            PhotoStatus::tryFrom($filters->statusId)
        );
    }

    /**
     * Prepare filters for the search photos list.
     *
     * @param PhotoSearchInputFiltersDto $filters Raw filters from request
     *
     * @return PhotoSearchFiltersDto Result filters
     *
     * @throws NonUniqueResultException
     */
    private function prepareSearchFilters(PhotoSearchInputFiltersDto $filters): PhotoSearchFiltersDto
    {
        return new PhotoSearchFiltersDto(
            null !== $filters->galleryId ? $this->galleryService->findOneById($filters->galleryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
            PhotoStatus::tryFrom($filters->statusId),
            $filters->titleId,
            $filters->descriptionId,
        );
    }
}
