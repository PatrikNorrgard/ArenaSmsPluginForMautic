<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces;


interface SmsGatewayInterface
{
    /** Getters */
    public function getId();

    public function getUserId();

    /** Setters */

    public function setUserId($id);
}