<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Form\Type;

use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces\ApiInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber());
        $builder
            ->add('provider', 'choice',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateway.create.form.field.label.provider',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'choices' => [
                        ApiInterface::AIMO_NAME => ucfirst(ApiInterface::AIMO_NAME),
                        ApiInterface::ENGINE_NAME  => ucfirst(ApiInterface::ENGINE_NAME),
                    ],
                    'required'    => true,
                    'empty_value' => 'plugin.smsgateway.create.form.field.option.default',
                ]
            )
            ->add('username', 'text',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateway.create.form.field.label.username',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => true,
                ]
            )
            ->add('password', 'password',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateway.create.form.field.label.password',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => true,
                ]
            )
            ->add('client_id', 'text',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateway.create.form.field.label.client_id',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => false,
                ]
            )
            ->add('callback_url', 'text',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateway.create.form.field.label.callback_url',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => false,
                ]
            )
            ->add('sender', 'text',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateway.create.form.field.label.sender',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                        'maxlength' => 11,
                    ],
                    'required'    => true,
                ]
            )
            ->add('default_provider', ChoiceType::class,
                [
                    'mapped' => false,
                    'label' => 'plugin.smsgateway.create.form.field.label.default',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'choices' => [
                        '0'  => 'No',
                        '1' => 'Yes',
                    ],
                    'required'    => true,
                    'multiple' => false,
                    'expanded' => false,
                ]
            )
            ->add('buttons', 'form_buttons');
    }
}