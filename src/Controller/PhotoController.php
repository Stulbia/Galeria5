<?php

/**
 * Photo controller.
 */

namespace App\Controller;

use App\Dto\PhotoListInputFiltersDto;
use App\Dto\PhotoSearchInputFiltersDto;
use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\Rating;
use App\Form\Type\CommentType;
use App\Form\Type\PhotoEditType;
use App\Form\Type\PhotoType;
use App\Form\Type\RatingType;
use App\Form\Type\SearchPhotoType;
use App\Resolver\PhotoListInputFiltersDtoResolver;
use App\Resolver\PhotoSearchInputFiltersDtoResolver;
use App\Service\CommentServiceInterface;
use App\Service\PhotoServiceInterface;
use App\Service\RatingServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PhotoController.
 */
#[Route('/photo')]
class PhotoController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param PhotoServiceInterface   $photoService   Photo service
     * @param CommentServiceInterface $commentService Comment service
     * @param RatingServiceInterface  $ratingService  Rating service
     * @param TranslatorInterface     $translator     Translator
     */
    public function __construct(private readonly PhotoServiceInterface $photoService, private readonly CommentServiceInterface $commentService, private readonly RatingServiceInterface $ratingService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param PhotoListInputFiltersDto $filters Input filters
     * @param int                      $page    Page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'photo_index', methods: 'GET')]
    public function index(#[MapQueryString(resolver: PhotoListInputFiltersDtoResolver::class)] PhotoListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->photoService->getPaginatedList($page, $filters);

        return $this->render('photo/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Top action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route('/top', name: 'photo_top', methods: 'GET')]
    public function top(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->ratingService->findPhotoOrder($page);

        return $this->render('photo/top.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Search action.
     *
     * @param Request                    $request HTTP request
     * @param PhotoSearchInputFiltersDto $filters Input filters
     * @param int                        $page    Page number
     *
     * @return Response HTTP response
     */
    #[Route('/search', name: 'photo_search', methods: 'GET')]
    public function search(Request $request, #[MapQueryString(resolver: PhotoSearchInputFiltersDtoResolver::class)] PhotoSearchInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $form = $this->createForm(SearchPhotoType::class, ['action' => $this->generateUrl('photo_search')]);
        $pagination = $this->photoService->getSearchList($page, $filters);
        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form->getData();
            $description = $form->getData();
            $filters->descriptionId = $description;
            $filters->titleId = $title;

            return $this->render('photo/search.html.twig', ['pagination' => $pagination, 'form' => $form->createView()]);
        }

        return $this->render('photo/search.html.twig', ['pagination' => $pagination, 'form' => $form->createView()]);
    }

    /**
     * Show action.
     *
     * @param Photo $photo Photo entity
     * @param int   $page  Page number
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'photo_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Photo $photo, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->commentService->findByPhoto($photo, $page);
        $rating = $this->ratingService->findAverageRatingByPhoto($photo);

        return $this->render('photo/show.html.twig', ['photo' => $photo, 'pagination' => $pagination, 'rating' => $rating]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'photo_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        $photo = new Photo();
        $photo->setAuthor($user);
        $photo->setFilename(' ');
        $form = $this->createForm(PhotoType::class, $photo, ['method' => 'POST', 'action' => $this->generateUrl('photo_create')]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $gallery = $photo->getGallery();
            if ($this->isGranted('EDIT', $gallery)) {
                $this->photoService->save($photo, $file, $user);

                $this->addFlash('success', $this->translator->trans('message.created_successfully'));
            } else {
                $this->addFlash('error', $this->translator->trans('message.incorrect_gallery'));
            }

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('photo/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Photo   $photo   Photo entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'photo_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'photo')]
    public function edit(Request $request, Photo $photo): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PhotoEditType::class, $photo, [
            'method' => 'PUT',
            'action' => $this->generateUrl('photo_edit', ['id' => $photo->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gallery = $photo->getGallery();
            if ($this->isGranted('EDIT', $gallery)) {
                $this->photoService->edit($photo);

                $this->addFlash('success', $this->translator->trans('message.created_successfully'));
            } else {
                $this->addFlash('error', $this->translator->trans('message.incorrect_gallery'));
            }

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('photo/edit.html.twig', ['form' => $form->createView(), 'photo' => $photo]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Photo   $photo   Photo entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'photo_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Photo $photo): Response
    {
        $form = $this->createForm(FormType::class, $photo, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('photo_delete', ['id' => $photo->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->photoService->delete($photo);

            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('photo/delete.html.twig', ['form' => $form->createView(), 'photo' => $photo]);
    }

    /**
     * Leave a Comment.
     *
     * @param Request $request HTTP request
     * @param Photo   $photo   Photo entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/comment', name: 'comment_create', methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function comment(Request $request, Photo $photo): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $this->commentService->save($comment, $user, $photo);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('photo_show', ['id' => $photo->getId()]);
        }

        return $this->render('photo/comment.html.twig', ['form' => $form->createView(), 'photo' => $photo]);
    }

    /**
     * Leave a Rating.
     *
     * @param Request $request HTTP request
     * @param Photo   $photo   Photo entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/rating', name: 'rating', methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function rate(Request $request, Photo $photo): Response
    {
        $user = $this->getUser();
        $rating = $this->ratingService->findByUserAndPhoto($user, $photo);
        if (null !== $rating) {
            $this->ratingService->delete($rating);
        }
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $this->ratingService->save($rating, $user, $photo);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('photo_show', ['id' => $photo->getId()]);
        }

        return $this->render('photo/rate.html.twig', ['form' => $form->createView(), 'photo' => $photo]);
    }

    /**
     * List of ratings for a photo.
     *
     * @param Photo $photo Photo entity
     * @param int   $page  Page
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/rating/list', name: 'rating_list', methods: 'GET|POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function ratingList(Photo $photo, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->ratingService->findByPhoto($photo, $page);

        return $this->render('photo/rating_list.html.twig', ['pagination'  => $pagination, 'photo' => $photo]);
    }

    /**
     * Deletes a given rating.
     *
     * @param Request $request HTTP request
     * @param Rating  $rating  Rating entity
     *
     * @return Response HTTP response
     */
    #[Route('/rating/{id}', name: 'rating_delete', methods: 'GET|DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    public function ratingDelete(Request $request, Rating $rating): Response
    {
        $form = $this->createForm(FormType::class, $rating, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('rating_delete', ['id' => $rating->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $rating->getPhoto();
            $this->ratingService->delete($rating);

            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('rating_list', ['id' => $photo->getId()]);
        }

        return $this->render('photo/rating_delete.html.twig', ['form' => $form->createView(), 'rating' => $rating]);
    }
}
