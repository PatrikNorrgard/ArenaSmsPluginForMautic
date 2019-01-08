<?php

namespace MauticPlugin\MauticSmsGateawayBundle\Form\Type;

use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
class SettingsEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $options['data'];

        $builder->addEventSubscriber(new CleanFormSubscriber());
        $builder
            ->add('username', 'text',
                [
                    'mapped'     => true,
                    'label'      => 'plugin.smsgateaway.create.form.field.label.username',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => true,
                    'data' => $data->getUsername(),
                ]
            )
            ->add('password', 'password',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateaway.create.form.field.label.password',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => true,
                    'data' => $data->getPassword(),
                ]
            )
            ->add('client_id', 'text',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateaway.create.form.field.label.client_id',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => true,
                    'data' => $data->getClientId(),
                ]
            )
            ->add('callback_url', 'text',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateaway.create.form.field.label.callback_url',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'required'    => false,
                    'data' => $data->getCallbackUrl(),
                ]
            )
            ->add('sender', 'text',
                [
                    'mapped'     => false,
                    'label'      => 'plugin.smsgateaway.create.form.field.label.sender',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                        'maxlength' => 11,
                    ],
                    'required'    => true,
                    'data' => $data->getSender(),
                ]
            )
            ->add('default_provider', 'choice',
                [
                    'mapped' => false,
                    'label' => 'plugin.smsgateaway.create.form.field.label.default',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'choices' => [
                        '0'  => 'No',
                        '1' => 'Yes',
                    ],
                    'required'    => true,
                ]
            )
            ->add('buttons', 'form_buttons');
    }
}