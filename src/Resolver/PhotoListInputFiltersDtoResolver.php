<?php

/**
 * PhotoListInputFiltersDto resolver.
 */

namespace App\Resolver;

use App\Dto\PhotoListInputFiltersDto;
use App\Entity\Enum\PhotoStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * PhotoListInputFiltersDtoResolver class.
 */
class PhotoListInputFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * Returns the possible value(s).
     *
     * @param Request          $request  HTTP Request
     * @param ArgumentMetadata $argument Argument metadata
     *
     * @return iterable Iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_a($argumentType, PhotoListInputFiltersDto::class, true)) {
            return [];
        }

        $galleryId = $request->query->get('galleryId');
        $tagId = $request->query->get('tagId');
        $statusId = $request->query->get('statusId', PhotoStatus::PUBLIC->value);

        return [new PhotoListInputFiltersDto($galleryId, $tagId, $statusId)];
    }
}
