<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Controller;


use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\MauticSmsGatewayBundle\Entity\SmsGatewaySettings;
use MauticPlugin\MauticSmsGatewayBundle\Entity\SmsGatewayStatus;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces\ApiInterface;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Traits\ApiTrait;
use MauticPlugin\MauticSmsGatewayBundle\Form\Type\UpdateStatusesType;
use Symfony\Component\HttpFoundation\Request;

class StatusController extends CommonController
{
    use ApiTrait;

    public function indexAction()
    {
        $form = $this->createForm(UpdateStatusesType::class, null, [
            'action' => $this->generateUrl('plugin_smsgateway_statuses_update'),
            'method' => Request::METHOD_POST,
            'userId' => $this->getUser()->getId(),
        ]);

        return $this->delegateView([
            'contentTemplate' => 'MauticSmsGatewayBundle:Status:index.html.php',
            'viewParameters' => [
                'form' => $form->createView(),
            ],
        ]);
    }

    public function updateAction(Request $request)
    {
        $ticketIds = $request->request->get('update_statuses')['tickets'];
        $tickets = $this->getStatusEntities($ticketIds);

        $provider = $this->getDoctrine()
            ->getRepository(SmsGatewaySettings::class)
            ->findOneBy([
                'userId' => $this->getUser()->getId(),
                'provider' => ApiInterface::AIMO_NAME,
            ]);

        $updatedTickets = $this->getStatusesAimo($tickets, [
            'client_id' => $provider->getClientId(),
            'user' => $provider->getUsername(),
            'pass' => $provider->getPassword(),
        ]);

        $entityManager = $this->getDoctrine()->getManager();
        foreach ($updatedTickets as $updatedTicket) {
            $entityManager->persist($updatedTicket);
        }
        $entityManager->flush();

        return $this->postActionRedirect([
            'returnUrl'       => $this->generateUrl('plugin_smsgateway_statuses'),
            'contentTemplate' => 'MauticSmsGatewayBundle:Status:index',
        ]);
    }

    private function getStatusEntities($ticketIds)
    {
        return $this->getDoctrine()
            ->getRepository(SmsGatewayStatus::class)
            ->findBy([
                'userId' => $this->getUser()->getId(),
                'ticketId' => $ticketIds,
            ],
                ['id' => 'DESC']
            );
    }
}