<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces;


interface SmsGatewayStatusInterface extends SmsGatewayInterface
{
    /** Getters */

    public function getStatus();

    public function getTicketId();

    public function getDeliveredDate();

    public function getPhone();

    /** Setters */

    public function setStatus($status);

    public function setTicketId($id);

    public function setDeliveredDate($date);

    public function setPhone($phone);
}