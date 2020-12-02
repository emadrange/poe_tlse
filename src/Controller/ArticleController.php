<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/article")
 * IsGranted("IS_AUTHENTICATED_FULLY")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/last", name="article_last", methods={"GET"})
     */
    public function last(ArticleRepository $articleRepository)
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findLast(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ): Response {
        $article = new Article();

        if ($this->getUser()->getAuthor() === null) {
            $this->addFlash('warning', $translator->trans('article.new.warning'));

            return $this->redirectToRoute('edit_profile', ['id' => $this->getUser()->getId()]);
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request); // $article->title = $_POST['title'];

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser()->getAuthor());
            $entityManager->persist($article); // INSERT INTO article (bla...) VALUES (blabla...)
            $entityManager->flush();

            $this->addFlash(
                'success',
                $translator->trans('article.new.success', ['%title%' => $article->getTitle()])
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET", "POST"})
     */
    public function show(
        Article $article,
        EntityManagerInterface $entityManager,
        CommentRepository $commentRepository,
        Request $request
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article)
                ->setAuthor($this->getUser()->getAuthor());
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('article_show', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comment_form' => $form->createView(),
            'comments' => $commentRepository->findBy([
                'article' => $article,
            ])
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","PUT"})
     */
    public function edit(
        Request $request,
        Article $article,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ): Response {
        $this->denyAccessUnlessGranted('ARTICLE_EDIT', $article);
        $form = $this->createForm(ArticleType::class, $article, [
            'method' => 'put',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash(
                'success',
                $translator->trans('article.edit.success', ['%title%' => $article->getTitle()])
            );

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }
}
