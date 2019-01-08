<?php

namespace MauticPlugin\MauticSmsGateawayBundle\Entity\Interfaces;


interface SmsGateawayStatusInterface extends SmsGateawayInterface
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