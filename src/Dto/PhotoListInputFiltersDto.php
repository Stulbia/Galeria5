<?php
/**
 * Photo list input filters DTO.
 */

namespace App\Dto;

/**
 * Class PhotoListInputFiltersDto.
 */
class PhotoListInputFiltersDto
{
    /**
     * Constructor.
     *
     * @param int|null $galleryId Gallery identifier
     * @param int|null $tagId     Tag identifier
     * @param int      $statusId  Status identifier
     */
    public function __construct(public readonly ?int $galleryId = null, public readonly ?int $tagId = null, public readonly string $statusId = 'PUBLIC')
    {
    }
}
