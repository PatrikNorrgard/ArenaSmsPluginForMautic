<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Integration;


use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticSmsGatewayBundle\Entity\SmsGatewaySettings;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces\ApiInterface;

class ArenaIntegration extends AbstractIntegration
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Arena';
    }

    /**
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'none';
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'plugins/MauticSmsGatewayBundle/Assets/img/arena.png';
    }

    /**
     * @return array
     */
    public function getFormSettings()
    {
        return [
            'requires_callback' => false,
            'requires_authorization' => false,
        ];
    }

    /**
     * @param \Mautic\PluginBundle\Integration\Form|\Symfony\Component\Form\FormBuilder $builder
     * @param array $data
     * @param string $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        $userId = $this->factory->get('mautic.helper.user')->getUser()->getId();
        $endpoints = $this->em
            ->getRepository(SmsGatewaySettings::class)
            ->createQueryBuilder('c')
            ->select('c.callbackUrl')
            ->where("c.userId = $userId")
            ->andWhere("c.provider = 'engine'")
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getArrayResult();
        foreach ($endpoints as $key => $endpoint) {
            $endpoints[$endpoint['callbackUrl']] = $endpoint['callbackUrl'];
            unset($endpoints[$key]);
            unset($endpoint);
        }

        if ($formArea == 'features') {
            $builder->add('provider', 'choice',
                [
                    'label' => 'plugin.smsgateway.create.form.field.label.provider',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'choices' => [
                        ApiInterface::AIMO_NAME => ucfirst(ApiInterface::AIMO_NAME),
                        ApiInterface::ENGINE_NAME => ucfirst(ApiInterface::ENGINE_NAME),
                    ],
                    'required' => true,
                    'empty_value' => 'plugin.smsgateway.create.form.field.option.provider.default',
                ]
            );
            $builder->add('callback_url', 'choice',
                [
                    'label' => 'plugin.smsgateway.create.form.field.label.callback_url',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'required' => false,
                    'choices' => $endpoints,
                    'empty_value' => 'plugin.smsgateway.create.form.field.option.endpoint.default',
                ]
            );
            $builder->add('user_id', 'hidden',
                [
                    'data' => $userId,
                ]
            );
        }
    }
}
