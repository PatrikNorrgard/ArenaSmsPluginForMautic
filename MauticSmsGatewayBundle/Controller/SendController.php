<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Controller;


use FOS\RestBundle\Util\Codes;
use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\MauticSmsGatewayBundle\Entity\SmsGatewaySettings;
use MauticPlugin\MauticSmsGatewayBundle\Entity\SmsGatewayStatus;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Traits\ApiTrait;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces\ApiInterface;
use MauticPlugin\MauticSmsGatewayBundle\Form\Type\SendSmsType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SendController extends CommonController implements ApiInterface
{
    use ApiTrait;

    public function indexAction()
    {
        $userId = $this->getUser()->getId();
        $providers = $this->getDoctrine()
            ->getRepository(SmsGatewaySettings::class)
            ->findBy(['userId' => $userId]);

        $form = $this->createForm(SendSmsType::class, null, [
            'action' => $this->generateUrl('plugin_smsgateway_send_message_post'),
            'method' => Request::METHOD_POST,
            'userId' => $userId,
        ]);

        return $this->delegateView([
            'contentTemplate' => 'MauticSmsGatewayBundle:Send:index.html.php',
            'viewParameters' => [
                'providers' => $providers,
                'form' => $form->createView(),
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return bool|string
     */
    public function sendAction(Request $request)
    {
        $transport = $this->get('mautic.sms.transport_chain')->getEnabledTransports();
        if (!$transport) {
            return new JsonResponse(json_encode(['error' => ['message' => 'SMS transport is disabled.', 'code' => Codes::HTTP_EXPECTATION_FAILED]]));
        }
        // Set params array to variable for more comfortable
        $params = $request->request->get('send_sms');
        // Getting plugin settings
        $params['provider'] = $transport['mautic.sms.transport.arena']->getProvider();
        // Getting provider entity
        $provider = $this->getEntityProvider($params['provider']);

        $phones = $params['contacts'];
        $credentials = $this->prepareData($params['provider'], $params['message']);
        if (empty($transport['mautic.sms.transport.arena']->getEndpoint())) {
            $baseUrl = $this->getBaseUrl($params['provider']);
        } else {
            $baseUrl = $transport['mautic.sms.transport.arena']->getEndpoint() . '?';
        }

        // Flash message to show that credits on balance is not enough
        $flashMessage = 'plugin.smsgateway.flash.send.low_credit';

        if ($params['provider'] === 'engine') {
            $balance = $provider->getBalance();
        } else {
            try {
                $balance = $this->getCredit($credentials, $params['provider']);
            } catch (\Exception $e) {
                $flashMessage = $e->getMessage();
            }
        }

        $currentBalance = $balance - ($params['messages_count'] * count($phones));

        if ($currentBalance > 0) {
            $response = $this->sendMessage($baseUrl, $credentials, $phones, $params['provider']);
            $flashMessage = 'plugin.smsgateway.flash.send.success';
            $this->saveData($response, $params['provider'], $currentBalance);
        } elseif ($balance === null && $params['provider'] == 'engine') {
            $response = $this->sendMessage($baseUrl, $credentials, $phones, $params['provider']);
            $flashMessage = 'plugin.smsgateway.flash.send.success';
            $this->saveData($response, $params['provider'], $currentBalance);
        }

        $this->addFlash($flashMessage);

        return $this->postActionRedirect([
            'returnUrl' => $this->generateUrl('plugin_smsgateway_send_message_get'),
            'contentTemplate' => 'MauticSmsGatewayBundle:Send:index',
        ]);
    }

    private function saveData($response, $provider, $currentBalance)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityProvider = $this->getEntityProvider($provider);

        switch ($provider) {
            case 'aimo':
                // If one phone
                $responses[0] = explode(':', $response);
                // If many phones than explode them
                if (stristr($response, ';')) {
                    $responses = explode(';', $response);
                    foreach ($responses as $key => $value) {
                        $responses[$key] = explode(':', $value);
                    }
                }

                // Save Data
                foreach ($responses as $value) {
                    $status = new SmsGatewayStatus();
                    $status->setUserId($this->getUser()->getId());
                    $status->setTicketId($value[1]);
                    $status->setPhone($value['2']);
                    $status->setDeliveredDate(date('Y-m-d H:i:s'));
                    if ($value[0] == 'OK') {
                        $status->setStatus(ApiInterface::STATUS_DELIVERED);
                    } else {
                        $status->setStatus(ApiInterface::STATUS_FAILED);
                    }
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($status);
                    $entityManager->flush();
                }
                $entityProvider->setBalance($currentBalance);
                break;
            case 'engine':
                if (isset($response['delivery-report']['accepted']['recipients']['recipient']['@id'])) {
                    $recepients[] = $response['delivery-report']['accepted']['recipients']['recipient'];
                } else {
                    $recepients = $response['delivery-report']['accepted']['recipients']['recipient'];
                }
                foreach ($recepients as $recepient) {
                    // Engine use dots in name to separate value, but because of this will occurre a problem
                    // to get field ticket_id on Statuses page. So we will explode'em and
                    // store only the last value. ID contains of UserName.Phone.ID
                    $ticketId = explode('.', $recepient['@id']);
                    $status = new SmsGatewayStatus();
                    $status->setUserId($this->getUser()->getId());
                    $status->setTicketId($ticketId[2]);
                    $status->setPhone($recepient['$']);
                    $status->setStatus(ApiInterface::STATUS_DELIVERED);
                    $status->setDeliveredDate(date('Y-m-d H:i:s'));
                    $entityManager->persist($status);
                    $entityManager->flush();
                }

                if (!empty($response['delivery-report']['failed'])) {
                    foreach ($response['delivery-report']['failed'] as $failed) {
                        $status = new SmsGatewayStatus();
                        $status = $status->setStatus('failed');
                        $status->setPhone($failed['$']);
                        $status->setDeliveredDate(date('Y-m-d H:i:s'));
                    }
                }
                $entityProvider->setBalance($response['delivery-report']['credit-balance']);
                break;
        }

        $entityManager->persist($entityProvider);
        $entityManager->flush();
    }
}
