<?php

namespace MauticPlugin\MauticSmsGateawayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use MauticPlugin\MauticSmsGateawayBundle\Entity\Interfaces\SmsGateawaySettingsInterface;

class SmsGateawaySettings implements SmsGateawaySettingsInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="Mautic\UserBundle\Entity\User")
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $clientId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $callbackUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $provider;

    /**
     * @ORM\Column(type="tinyint")
     */
    private $defaultProvider;

    /**
     * @ORM\Column(type="integer")
     */
    private $balance;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder
            ->setTable('plugin_smsgateaway_settings')
            ->addIndex(['user_id'], 'user_id')
            ->addIndex(['username'], 'username')
            ->addIndex(['client_id'], 'client_id');
        $builder->addId();
        $builder->addNamedField('userId', 'integer', 'user_id');
        $builder->addNamedField('username', 'string', 'username');
        $builder->addNamedField('password', 'string', 'password');
        $builder->addNamedField('clientId', 'string', 'client_id', true);
        $builder->addNamedField('callbackUrl', 'string', 'callback_url', true);
        $builder->addNamedField('sender', 'string', 'sender');
        $builder->addNamedField('provider', 'string', 'provider');
        $builder->addNamedField('defaultProvider', 'integer', 'default_provider', true);
        $builder->addNamedField('balance', 'integer', 'balance', true);

        $builder->createManyToOne('userId', 'Mautic\UserBundle\Entity\User');
    }

    /** Getters */

    /** General */

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function getDefaultProvider()
    {
        return $this->defaultProvider;
    }
    
    public function getBalance()
    {
        return $this->balance;
    }

    /** Setters */

    public function setUserId($id)
    {
        $this->userId = $id;
    }

    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    public function setUsername($name)
    {
        $this->username = $name;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setClientId($id)
    {
        $this->clientId = $id;
    }

    public function setCallbackUrl($url)
    {
        $this->callbackUrl = $url;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    public function setDefaultProvider($value)
    {
        $this->defaultProvider = $value;
    }

    public function setBalance($value)
    {
        $this->balance = $value;
    }
}