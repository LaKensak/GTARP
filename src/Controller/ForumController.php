<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\Theme;
use App\Form\DiscussionType;
use App\Form\ThemeType;
use App\Repository\DiscussionRepository;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ForumController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ThemeRepository $themeRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $page = $request->query->getInt('page', 1);

        $themes = $themeRepository->findAllPaginated($paginator, $page);

        return $this->render('forum/index.html.twig', [
            'themes' => $themes,
        ]);
    }

    #[Route('/theme/{id}', name: 'app_theme_show')]
    public function showTheme(
        Theme $theme,
        DiscussionRepository $discussionRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $page = $request->query->getInt('page', 1);

        $discussions = $discussionRepository->findByThemePaginated($theme, $paginator, $page);

        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);

        return $this->render('forum/theme.html.twig', [
            'theme' => $theme,
            'discussions' => $discussions,
            'discussionForm' => $form->createView(),
        ]);
    }

    #[Route('/theme/{id}/discussion/ajouter', name: 'app_discussion_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addDiscussion(
        Theme $theme,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response|JsonResponse {
        // add
        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // bind
            $discussion->setTheme($theme);
            $discussion->setAuteur($this->getUser());

            $entityManager->persist($discussion);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                $html = $this->renderView('forum/_discussion_item.html.twig', [
                    'discussion' => $discussion,
                ]);

                return $this->json([
                    'ok' => true,
                    'html' => $html,
                ]);
            }

            $this->addFlash('success', 'Votre discussion a été ajoutée !');
        } else {
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'ok' => false,
                    'error' => 'Erreur lors de l\'ajout de la discussion',
                ], 400);
            }

            $this->addFlash('error', 'Erreur lors de l\'ajout de la discussion');
        }

        return $this->redirectToRoute('app_theme_show', ['id' => $theme->getId()]);
    }

    #[Route('/theme/nouveau', name: 'app_theme_new', priority: 10)]
    #[IsGranted('ROLE_MODERATOR')]
    public function newTheme(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();

            $this->addFlash('success', 'Le thème a été créé !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('forum/theme_new.html.twig', [
            'themeForm' => $form->createView(),
        ]);
    }

    #[Route('/discussion/{id}/modifier', name: 'app_discussion_edit')]
    #[IsGranted('ROLE_MODERATOR')]
    public function editDiscussion(
        Discussion $discussion,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discussion->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'La discussion a été modifiée !');
            return $this->redirectToRoute('app_theme_show', [
                'id' => $discussion->getTheme()->getId()
            ]);
        }

        return $this->render('forum/discussion_edit.html.twig', [
            'discussion' => $discussion,
            'discussionForm' => $form->createView(),
        ]);
    }

    #[Route('/discussion/{id}/supprimer', name: 'app_discussion_delete')]
    #[IsGranted('ROLE_MODERATOR')]
    public function deleteDiscussion(
        Discussion $discussion,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('delete' . $discussion->getId(), $request->request->get('_token'))) {
                $themeId = $discussion->getTheme()->getId();
                $entityManager->remove($discussion);
                $entityManager->flush();

                $this->addFlash('success', 'La discussion a été supprimée !');
                return $this->redirectToRoute('app_theme_show', ['id' => $themeId]);
            }
        }

        return $this->render('forum/discussion_delete.html.twig', [
            'discussion' => $discussion,
        ]);
    }
}
