<?php
/**
 * Created by PhpStorm.
 * User: Y520
 * Date: 23.05.2018
 * Time: 11:06
 */

namespace MauticPlugin\MauticSmsGatewayBundle\Form\Type;

use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Entity\LeadRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SendSmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userId = $options['userId'];

        $builder->addEventSubscriber(new CleanFormSubscriber());
        $builder
            ->add('message', 'textarea',
                [
                    'mapped' => false,
                    'label' => 'plugin.smsgateway.send.index.form.label.message',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'required' => true,
                ]
            )
            ->add('contacts', EntityType::class, [
                'class' => 'MauticLeadBundle:Lead',
                'query_builder' => function (LeadRepository $er) use ($userId) {
                    return $er->createQueryBuilder('c')
                        ->where("c.owner = $userId")
                        ->andWhere("c.mobile != ''")
                        ->orderBy('c.id', 'DESC');
                },
                'choice_label' => function (Lead $entity = null) {
                    return $entity ? $entity->getFirstName() . ' ' . $entity->getLastName() . '|' . $entity->getMobile() : '';
                },
                'choice_value' => function (Lead $entity = null) {
                    return $entity ? str_replace('+', '', $entity->getMobile()) : '';
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('messages_count', 'hidden', [
                'mapped' => false,
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Send',
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'userId' => null,
        ]);
    }
}