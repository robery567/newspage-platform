<?php
namespace AppBundle\Controller\Panel;

use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;
use AppBundle\WebService\MmttWebService;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ArticleController
 * @package AppBundle\Controller\Panel
 * @Route("/redactor")
 * @Security("is_granted('ROLE_REDACTOR')")
 */
class ArticleController extends Controller
{
    /**
     * Checks if user haves permission to delete an article
     * @return bool
     */
    private function canDelete(): bool
    {
        return $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
    }

    /**
     * Creates a form to delete a article entity.
     * @param Article $article The article entity
     * @return FormInterface
     */
    private function createDeleteForm(Article $article): FormInterface
    {
        return $this->createFormBuilder()->setAction($this->generateUrl('panel_article_delete',
            array('id' => $article->getId())))->setMethod('DELETE')->getForm();
    }

    /**
     * Lists all article entities.
     * @Route("/", defaults={"page": "1"}, name="panel_article_index")
     * @Route("/pagina/{page}", requirements={"page": "[1-9]\d*"}, name="panel_article_index_paginated")
     * @Method("GET")
     */
    public function indexAction($page): Response
    {
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAllPaginated($page);

        return $this->render('Panel/redactor_article_index.html.twig', array(
            'articles' => $articles,
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/articol/video", name="panel_article_video_list")
     * @Method("GET")
     */
    public function listVideoArticleAction(): Response
    {
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAllVideo();

        return $this->render('Panel/redactor_article_index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/articol/recomandat", name="panel_article_recommended_list")
     * @Method("GET")
     */
    public function listRecommendedArticleAction(): Response
    {
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAllRecommended();

        return $this->render('Panel/redactor_article_index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/articol/hot", name="panel_article_hot_list")
     * @Method("GET")
     */
    public function listHotArticleAction(): Response
    {
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAllHot();

        return $this->render('Panel/redactor_article_index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/creare/{uuid}", name="panel_article_create")
     * @Method("POST")
     */
    public function processAction(Request $request, string $uuid): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($article->getType() !== Article::TYPE_VIDEO) {
                /** @var MmttWebService $thumbnail */
                $thumbnail = $this->get(MmttWebService::class);
                $content = $form->get('content')->getData();
                $thumbnailUrl = $thumbnail->getThumbnail($content);

                $article->setMedia($thumbnailUrl);
            }

            $article->setAuthor($this->getUser());
            $article->setArticleId($uuid);

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('panel_article_show', ['id' => $article->getId()]);
        }
    }

    /**
     * Creates a new article entity.
     * @Route("/adaugare", name="panel_article_new")
     * @Route("/adaugare/video", name="panel_article_video_new")
     * @Method("GET")
     */
    public function newAction(Request $request): Response
    {
        $article = new Article();
        $em = $this->getDoctrine()->getManager();
        $articleId = Uuid::uuid4();

        $form = $this->createForm('AppBundle\Form\ArticleType', $article, [
            'action' => $this->generateUrl('panel_article_create', [
                'uuid' => $articleId,
            ]),
            'method' => 'POST'
        ]);

        return $this->render('Panel/redactor_article_new.html.twig', array(
            'article' => $article,
            'articleId' => $articleId,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a article entity.
     * @Route("/info/{id}", name="panel_article_show")
     * @Method("GET")
     */
    public function showAction(Article $article): Response
    {
        $twigVars = [
            'article' => $article,
        ];

        if ($this->canDelete()) {
            $deleteForm = $this->createDeleteForm($article);
            $twigVars['delete_form'] = $deleteForm->createView();
        }

        return $this->render('Panel/redactor_article_show.html.twig', $twigVars);
    }

    /**
     * Displays a form to edit an existing article entity.
     * @Route("/actualizare/{id}", name="panel_article_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Article $article): Response
    {
        $twigVars = [
            'article' => $article,
        ];

        if ($this->canDelete()) {
            $deleteForm = $this->createDeleteForm($article);
            $twigVars['delete_form'] = $deleteForm->createView();
        }

        $editForm = $this->createForm('AppBundle\Form\ArticleType', $article);
        $editForm->handleRequest($request);

        $twigVars['edit_form'] = $editForm->createView();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panel_article_index');
        }

        return $this->render('Panel/redactor_article_edit.html.twig', $twigVars);
    }

    /**
     * Deletes a article entity.
     * @Route("/stergere/{id}", name="panel_article_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, Article $article): Response
    {
        $form = $this->createDeleteForm($article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute('panel_article_index');
    }
}