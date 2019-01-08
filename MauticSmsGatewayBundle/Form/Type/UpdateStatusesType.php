<?php

namespace MauticPlugin\MauticSmsGateawayBundle\Form\Type;


use Doctrine\ORM\EntityRepository;
use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use MauticPlugin\MauticSmsGateawayBundle\Entity\SmsGateawayStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UpdateStatusesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userId = $options['userId'];

        $builder->addEventSubscriber(new CleanFormSubscriber());

        $builder
            ->add('tickets', EntityType::class, [
                'class' => 'MauticSmsGateawayBundle:SmsGateawayStatus',
                'query_builder' => function (EntityRepository $er) use ($userId) {
                    return $er->createQueryBuilder('c')
                        ->where("c.userId = $userId")
                        ->orderBy('c.id', 'DESC');
                },
                'choice_value' => function (SmsGateawayStatus $entity = null) {
                    return $entity ? str_replace('+', '',$entity->getTicketId()) : '';
                },
                'choice_label' => function (SmsGateawayStatus $entity = null) {
                    return $entity ? $entity->getPhone() . '|' . $entity->getTicketId() . '|' . $entity->getStatus() . '|' . $entity->getDeliveredDate()->format('Y-m-d H:i:s') : '';
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('update', SubmitType::class, [
                'label' => 'Update',
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