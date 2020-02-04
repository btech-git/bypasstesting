<?php

namespace AppBundle\Controller\Master;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Master\PaymentMethod;
use AppBundle\Form\Master\PaymentMethodType;
use AppBundle\Grid\Master\PaymentMethodGridType;

/**
 * @Route("/master/payment_method")
 */
class PaymentMethodController extends Controller
{
    /**
     * @Route("/grid", name="master_payment_method_grid", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function gridAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(PaymentMethod::class);

        $grid = $this->get('lib.grid.datagrid');
        $grid->build(PaymentMethodGridType::class, $repository, $request);

        return $this->render('master/payment_method/grid.html.twig', array(
            'grid' => $grid->createView(),
        ));
    }

    /**
     * @Route("/", name="master_payment_method_index")
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function indexAction()
    {
        return $this->render('master/payment_method/index.html.twig');
    }

    /**
     * @Route("/new", name="master_payment_method_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function newAction(Request $request)
    {
        $paymentMethod = new PaymentMethod();
        $form = $this->createForm(PaymentMethodType::class, $paymentMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PaymentMethod::class);
            $repository->add($paymentMethod);

            return $this->redirectToRoute('master_payment_method_show', array('id' => $paymentMethod->getId()));
        }

        return $this->render('master/payment_method/new.html.twig', array(
            'paymentMethod' => $paymentMethod,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="master_payment_method_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function showAction(PaymentMethod $paymentMethod)
    {
        return $this->render('master/payment_method/show.html.twig', array(
            'paymentMethod' => $paymentMethod,
        ));
    }

    /**
     * @Route("/{id}/edit", name="master_payment_method_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function editAction(Request $request, PaymentMethod $paymentMethod)
    {
        $form = $this->createForm(PaymentMethodType::class, $paymentMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(PaymentMethod::class);
            $repository->update($paymentMethod);

            return $this->redirectToRoute('master_payment_method_show', array('id' => $paymentMethod->getId()));
        }

        return $this->render('master/payment_method/edit.html.twig', array(
            'paymentMethod' => $paymentMethod,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/delete", name="master_payment_method_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_HUMAN_RESOURCE')")
     */
    public function deleteAction(Request $request, PaymentMethod $paymentMethod)
    {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository(PaymentMethod::class);
                $repository->remove($paymentMethod);

                $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
            } else {
                $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
            }

            return $this->redirectToRoute('master_payment_method_index');
        }

        return $this->render('master/payment_method/delete.html.twig', array(
            'paymentMethod' => $paymentMethod,
            'form' => $form->createView(),
        ));
    }
}
