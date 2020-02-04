<?php

namespace AppBundle\Controller\Staff;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/staff/secured")
 */
class SecuredController extends Controller
{
    /**
     * @Route("/", name="staff_secured_index")
     */
    public function indexAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('staff/secured/index.html.twig', array(
            'lastUsername' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * @Route("/login", name="staff_secured_login")
     */
    public function loginAction()
    {
    }

    /**
     * @Route("/logout", name="staff_secured_logout")
     */
    public function logoutAction()
    {
    }
}
