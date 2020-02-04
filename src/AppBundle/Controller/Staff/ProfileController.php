<?php

namespace AppBundle\Controller\Staff;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Common\UserPassword;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Form\Common\UserPasswordType;
use AppBundle\Form\Admin\StaffProfileType;

/**
 * @Route("/staff/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/", name="staff_profile_index")
     * @Method("GET")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function indexAction()
    {
        return $this->render('staff/profile/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/show/{id}", name="staff_profile_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_STAFF') and user.isEqualTo(staff)")
     */
    public function showAction(Staff $staff)
    {
        return $this->render('staff/profile/show.html.twig', array(
            'user' => $staff,
        ));
    }

    /**
     * @Route("/edit/{id}", name="staff_profile_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_STAFF') and user.isEqualTo(staff)")
     */
    public function editAction(Request $request, Staff $staff)
    {
        $form = $this->createForm(StaffProfileType::class, $staff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Staff::class);
            $repository->update($staff);

            return $this->redirectToRoute('staff_profile_show', array('id' => $staff->getId()));
        }

        return $this->render('staff/profile/edit.html.twig', array(
            'user' => $staff,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/password/{id}", name="staff_profile_password", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_STAFF') and user.isEqualTo(staff)")
     */
    public function passwordAction(Request $request, Staff $staff)
    {
        $userPassword = new UserPassword;
        $form = $this->createForm(UserPasswordType::class, $userPassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')->encodePassword($staff, $userPassword->getNewPassword());
            $staff->setPassword($password);
            
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository(Staff::class);
            $repository->update($staff);

            return $this->redirectToRoute('staff_profile_show', array('id' => $staff->getId()));
        }

        return $this->render('staff/profile/password.html.twig', array(
            'user' => $staff,
            'form' => $form->createView(),
        ));
    }
}
