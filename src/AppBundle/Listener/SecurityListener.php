<?php

namespace AppBundle\Listener;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $securityContext = $this->securityContext;
        
        $transactionRowMenu = array(
            'sale_discount_application',
            'sale_order',
            'purchase_workshop_header',
            'delivery_workshop',
            'receive_workshop',
            'delivery_order',
            'purchase_delivery_order',
            'receive_order',
            'delivery_inspection_header',
            'sale_invoice',
            'sale_payment',
            'purchase_invoice_header_unit',
            'purchase_invoice_header_workshop',
            'purchase_invoice_header_general',
            'purchase_payment_header',
        );
        $transactionRowMenu = array_combine($transactionRowMenu, $transactionRowMenu);
        $callback = function($value) use ($securityContext) {
            return $securityContext->isGranted($this->getTaskRoleNames($value));
        };
        $transactionRowMenu = array_map($callback, $transactionRowMenu);
        $transactionTableMenu = array(
            'sale' => array(0, 6),
            'purchase' => array(6, 2),
            'service' => array(8, 1),
            'finance' => array(9, 6),
        );
        $callback = function($value) use ($transactionRowMenu) {
            return array_reduce(array_slice($transactionRowMenu, $value[0], $value[1]), function($carry, $item) { return $carry || $item; }, false);
        };
        $transactionTableMenu = array_map($callback, $transactionTableMenu);
        
        $headerMenu = array(
            'master' => $securityContext->isGranted('ROLE_HUMAN_RESOURCE'),
            'report' => $securityContext->isGranted('ROLE_REPORT'),
            'staff' => $securityContext->isGranted('ROLE_ADMIN'),
            'transaction' => $transactionTableMenu['sale'] || $transactionTableMenu['purchase'] || $transactionTableMenu['service'] || $transactionTableMenu['finance'],
        );
        $menu = array_merge($transactionRowMenu, $transactionTableMenu, $headerMenu);
        
        $session = $event->getRequest()->getSession();
        $session->set('menu', $menu);
        $event->getRequest()->setSession($session);
    }

    private function getTaskRoleNames($module)
    {
        $taskRoleNames = array();
        foreach (array('NEW', 'EDIT', 'DELETE') as $task) {
            $taskRoleNames[] = 'ROLE_' . strtoupper($module) . '_' . $task;
        }
        return $taskRoleNames;
    }
}
