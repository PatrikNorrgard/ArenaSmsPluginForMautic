<?php

namespace MauticPlugin\MauticSmsGateawayBundle\Entity\Interfaces;


interface SmsGateawaySettingsInterface extends SmsGateawayInterface
{
    /** Getters */

    public function getSender();

    public function getUsername();

    public function getPassword();

    public function getClientId();

    public function getCallbackUrl();

    public function getProvider();

    public function getDefaultProvider();

    public function getBalance();

    /** Setters */

    public function setSender($sender);

    public function setUsername($name);

    public function setPassword($password);

    public function setClientId($id);

    public function setCallbackUrl($url);

    public function setProvider($provider);

    public function setDefaultProvider($value);

    public function setBalance($value);
}