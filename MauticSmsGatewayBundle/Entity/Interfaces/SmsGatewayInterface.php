<?php

namespace MauticPlugin\MauticSmsGateawayBundle\Entity\Interfaces;


interface SmsGateawayInterface
{
    /** Getters */
    public function getId();

    public function getUserId();

    /** Setters */

    public function setUserId($id);
}