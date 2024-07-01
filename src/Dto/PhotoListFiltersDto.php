<?php

/**
 * Photo list filters DTO.
 */

namespace App\Dto;

use App\Entity\Enum\PhotoStatus;
use App\Entity\Gallery;
use App\Entity\Tag;

/**
 * Class PhotoListFiltersDto.
 */
class PhotoListFiltersDto
{
    /**
     * Constructor.
     *
     * @param Gallery|null $gallery     Gallery entity
     * @param Tag|null     $tag         Tag entity
     * @param PhotoStatus  $photoStatus Photo status
     */
    public function __construct(public readonly ?Gallery $gallery, public readonly ?Tag $tag, public readonly PhotoStatus $photoStatus)
    {
    }
}
