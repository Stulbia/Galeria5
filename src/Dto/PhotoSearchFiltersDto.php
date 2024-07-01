<?php

/**
 * Photo search filters DTO.
 */

namespace App\Dto;

use App\Entity\Enum\PhotoStatus;
use App\Entity\Gallery;
use App\Entity\Tag;

/**
 * Class PhotoSearchFiltersDto.
 */
class PhotoSearchFiltersDto
{
    /**
     * Constructor.
     *
     * @param Gallery|null $gallery            Gallery entity
     * @param Tag|null     $tag                Tag entity
     * @param PhotoStatus  $photoStatus        Photo status
     * @param string|null  $titlePattern       Title pattern
     * @param string|null  $descriptionPattern Description pattern
     */
    public function __construct(public readonly ?Gallery $gallery, public readonly ?Tag $tag, public readonly PhotoStatus $photoStatus, public readonly ?string $titlePattern, public readonly ?string $descriptionPattern)
    {
    }
}
