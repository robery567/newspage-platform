<?php
namespace AppBundle\Controller\Panel;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController
 * @package AppBundle\Controller\Panel
 * @Route("/administrator/categorie")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class CategoryController extends Controller
{
    /**
     * Lists all category entities.
     * @Route("/", name="panel_category_index")
     * @Method("GET")
     */
    public function indexAction(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AppBundle:Category')->findAll();

        return $this->render('Panel/admin_category_index.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Finds and displays a category entity.
     * @Route("/informatii/{id}", name="panel_category_show")
     * @Method("GET")
     */
    public function showAction(Category $category): Response
    {
        return $this->render('Panel/admin_category_show.html.twig', array(
            'category'    => $category,
        ));
    }

    /**
     * Creates a new category entity.
     * @Route("/adauga", name="panel_category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm('AppBundle\Form\CategoryType', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('panel_category_show', array('id' => $category->getId()));
        }

        return $this->render('Panel/admin_category_new.html.twig', array(
            'category' => $category,
            'form'     => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing category entity.
     * @Route("/actualizeaza/{id}", name="panel_category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Category $category): Response
    {
        $editForm = $this->createForm('AppBundle\Form\CategoryType', $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panel_category_index', array('id' => $category->getId()));
        }

        return $this->render('Panel/admin_category_edit.html.twig', array(
            'category'    => $category,
            'edit_form'   => $editForm->createView(),
        ));
    }
}