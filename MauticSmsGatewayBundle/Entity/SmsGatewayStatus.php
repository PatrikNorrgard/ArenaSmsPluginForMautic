<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces\SmsGatewayStatusInterface;

class SmsGatewayStatus implements SmsGatewayStatusInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\OneToOne(targetEntity="Mautic\UserBundle\Entity\User")
     */
    private $userId;

    /**
     * @ORM\Column(type="string")
     */
    private $phone;

    /**
     * @ORM\Column(type="string")
     */
    private $ticketId;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deliveredDate;


    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder
            ->setTable('plugin_smsgateway_statuses')
            ->addIndex(['user_id'], 'user_id')
            ->addIndex(['ticket_id'], 'ticket_id');
        $builder->addId();
        $builder->addNamedField('userId', 'integer', 'user_id');
        $builder->addNamedField('phone', 'string', 'phone');
        $builder->addNamedField('ticketId', 'string', 'ticket_id', true);
        $builder->addNamedField('status', 'string', 'status');
        $builder->addNamedField('deliveredDate', 'datetime', 'delivered_date');

        $builder->createManyToOne('userId', 'Mautic\UserBundle\Entity\User');
    }

    /** Getters */

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getTicketId()
    {
        return $this->ticketId;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDeliveredDate()
    {
        return $this->deliveredDate;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    /** Setters */

    public function setUserId($id)
    {
        $this->userId = $id;
    }

    public function setTicketId($id)
    {
        $this->ticketId = $id;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setDeliveredDate($date)
    {
        $this->deliveredDate = $date;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
}