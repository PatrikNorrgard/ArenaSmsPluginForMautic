<?php

namespace MauticPlugin\MauticSmsGateawayBundle\Controller;


use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\MauticSmsGateawayBundle\Entity\SmsGateawaySettings;
use MauticPlugin\MauticSmsGateawayBundle\Entity\SmsGateawayStatus;
use MauticPlugin\MauticSmsGateawayBundle\Entity\Interfaces\ApiInterface;
use MauticPlugin\MauticSmsGateawayBundle\Entity\Traits\ApiTrait;
use MauticPlugin\MauticSmsGateawayBundle\Form\Type\UpdateStatusesType;
use Symfony\Component\HttpFoundation\Request;

class StatusController extends CommonController
{
    use ApiTrait;

    public function indexAction()
    {
        $form = $this->createForm(UpdateStatusesType::class, null, [
            'action' => $this->generateUrl('plugin_smsgateaway_statuses_update'),
            'method' => Request::METHOD_POST,
            'userId' => $this->getUser()->getId(),
        ]);

        return $this->delegateView([
            'contentTemplate' => 'MauticSmsGateawayBundle:Status:index.html.php',
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
            ->getRepository(SmsGateawaySettings::class)
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
            'returnUrl'       => $this->generateUrl('plugin_smsgateaway_statuses'),
            'contentTemplate' => 'MauticSmsGateawayBundle:Status:index',
        ]);
    }

    private function getStatusEntities($ticketIds)
    {
        return $this->getDoctrine()
            ->getRepository(SmsGateawayStatus::class)
            ->findBy([
                'userId' => $this->getUser()->getId(),
                'ticketId' => $ticketIds,
            ],
                ['id' => 'DESC']
            );
    }
}