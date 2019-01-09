<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\MauticSmsGatewayBundle\Entity\SmsGatewaySettings as Settings;
use MauticPlugin\MauticSmsGatewayBundle\Form\Type\SettingsEditType;
use MauticPlugin\MauticSmsGatewayBundle\Form\Type\SettingsType;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends CommonController
{
    /**
     * List of all providers
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $entities = $this->getDoctrine()
            ->getRepository(Settings::class)
            ->findBy([
                'userId' => $this->getUser()->getId()
            ]);

        return $this->delegateView([
            'contentTemplate' => 'MauticSmsGatewayBundle:Settings:index.html.php',
            'viewParameters' => [
                'entities' => $entities,
            ],
        ]);
    }

    /**
     * Display a form to create a new provider
     */
    public function createAction()
    {
        $userId = $this->getUser()->getId();
        $settings = new Settings();
        $settings->setUserId($userId);

        $form = $this->createForm(SettingsType::class, $settings, [
            'action' => $this->generateUrl('plugin_smsgateway_settings_provider_store'),
            'method' => Request::METHOD_POST,
        ]);

        return $this->delegateView([
            'contentTemplate' => 'MauticSmsGatewayBundle:Settings:provider_add.html.php',
            'viewParameters' => [
                'form' => $form->createView(),
            ],
        ]);
    }

    /**
     * Create a new provider
     *
     * @param Request $request
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function storeAction(Request $request)
    {
        $userId = $this->getUser()->getId();
        // Getting request params
        $requestParams = $request->request->get('settings');

        // If user clicked "Cancel"
        if (isset($requestParams['buttons']['cancel'])) {
            return $this->redirect($this->generateUrl('plugin_smsgateway_settings'));
        }

        // Applying request to entity
        $entity = new Settings();
        $entity->setUserId($userId);
        $entity->setProvider($requestParams['provider']);
        $entity->setUsername($requestParams['username']);
        $entity->setPassword($requestParams['password']);
        $entity->setClientId($requestParams['client_id']);
        $entity->setCallbackUrl($requestParams['callback_url']);
        $entity->setSender($requestParams['sender']);
        $entity->setDefaultProvider($requestParams['default_provider']);

        // Saving entity
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        $this->addFlash('plugin.smsgateway.flash.store.success');

        $redirectUrl = $this->generateUrl('plugin_smsgateway_settings');

        // If clicked "Apply"
        if (isset($requestParams['buttons']['apply'])) {
            $redirectUrl = $this->generateUrl('plugin_smsgateway_settings_provider_create');
        }

        return $this->postActionRedirect([
            'returnUrl'       => $redirectUrl,
            'contentTemplate' => 'MauticSmsGatewayBundle:Settings:index',
        ]);
    }

    /**
     * Show provider
     *
     * @param $providerId
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction($providerId)
    {
        $entity = $this->getDoctrine()
            ->getRepository(Settings::class)
            ->find($providerId);

        return $this->delegateView([
            'contentTemplate' => 'MauticSmsGatewayBundle:Settings:provider_show.html.php',
            'viewParameters' => [
                'entity' => $entity,
            ],
        ]);
    }

    /**
     * Edit provider
     *
     * @param $providerId
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($providerId)
    {
        $entity = $this->getDoctrine()
            ->getRepository(Settings::class)
            ->find($providerId);

        $form = $this->createForm(SettingsEditType::class, $entity, [
            'action' => $this->generateUrl('plugin_smsgateway_settings_provider_update', [
                'providerId' => $providerId,
            ]),
            'method' => Request::METHOD_POST,
        ]);

        return $this->delegateView([
            'contentTemplate' => 'MauticSmsGatewayBundle:Settings:provider_edit.html.php',
            'viewParameters' => [
                'entity' => $entity,
                'form' => $form->createView(),
            ],
        ]);
    }

    /**
     * Update provider
     *
     * @param Request $request
     * @param $providerId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Request $request, $providerId)
    {
        // Getting request params
        $requestParams = $request->request->get('settings_edit');

        // If user clicked "Cancel"
        if (isset($requestParams['buttons']['cancel'])) {
            return $this->redirect($this->generateUrl('plugin_smsgateway_settings'));
        }

        $entity = $this->getDoctrine()
            ->getRepository(Settings::class)
            ->find($providerId);
        $entity->setUsername($requestParams['username']);
        $entity->setPassword($requestParams['password']);
        $entity->setClientId($requestParams['client_id']);
        $entity->setCallbackUrl($requestParams['callback_url']);
        $entity->setSender($requestParams['sender']);
        $entity->setDefaultProvider($requestParams['default_provider']);

        // Saving entity
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        $this->addFlash('plugin.smsgateway.flash.update.success');

        $redirectUrl = $this->generateUrl('plugin_smsgateway_settings');

        // If clicked "Apply"
        if (isset($requestParams['buttons']['apply'])) {
            $redirectUrl = $this->generateUrl('plugin_smsgateway_settings_provider_create');
        }

        return $this->postActionRedirect([
            'returnUrl'       => $redirectUrl,
            'contentTemplate' => 'MauticSmsGatewayBundle:Settings:index',
        ]);
    }

    /**
     * Delete provider
     *
     * @param $providerId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($providerId)
    {
        $entity = $this->getDoctrine()
            ->getRepository(Settings::class)
            ->find($providerId);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($entity);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('plugin_smsgateway_settings'));
    }
}