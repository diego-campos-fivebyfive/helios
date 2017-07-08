<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Item;
use AppBundle\Form\Component\ItemType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("items")
 */
class ItemController extends AbstractController
{
    /**
     * @Route("/", name="items_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $items = $this->manager('item')->findAll();

        return $this->render('item.index', array(
            'items' => $items,
        ));
    }

    /**
     * @Route("/create", name="items_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('item');
        $item = $manager->create();

        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->save($item);

            return $this->redirectToRoute('items_index');
        }

        return $this->render('item.form', array(
            'item' => $item,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="items_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Item $item)
    {
        $editForm = $this->createForm(ItemType::class, $item);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->manager('item')->save($item);

            return $this->redirectToRoute('items_index');
        }

        return $this->render('item.form', array(
            'item' => $item,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="items_delete")
     * @Method("delete")
     */
    public function deleteAction(Item $item)
    {
        $this->manager('item')->delete($item);

        return $this->json([]);
    }
}
