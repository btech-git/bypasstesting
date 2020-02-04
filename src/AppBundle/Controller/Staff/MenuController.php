<?php

namespace AppBundle\Controller\Staff;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/staff/menu")
 */
class MenuController extends Controller
{
    /**
     * @Route("/master", name="staff_menu_master")
     * @Method("GET")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function masterAction()
    {
        return $this->render('staff/menu/master.html.twig');
    }

    /**
     * @Route("/report", name="staff_menu_report")
     * @Method("GET")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function reportAction()
    {
        return $this->render('staff/menu/report.html.twig');
    }

    /**
     * @Route("/transaction", name="staff_menu_transaction")
     * @Method("GET")
     * @Security("has_role('ROLE_STAFF')")
     */
    public function transactionAction()
    {
        return $this->render('staff/menu/transaction.html.twig');
    }
}
