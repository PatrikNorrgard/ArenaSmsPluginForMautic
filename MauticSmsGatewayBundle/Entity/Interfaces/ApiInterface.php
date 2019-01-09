<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces;


interface ApiInterface
{
    const AIMO_NAME = 'aimo';
    const ENGINE_NAME = 'engine';

    const AIMO_BASE_URL = 'https://www.apmobile.net/api/';
    const AIMO_ACTION_AUTH = 'auth.aspx?';
    const AIMO_ACTION_SENDMSG = 'sendmsg.aspx?';
    const AIMO_ACTION_PING = 'ping.aspx?';
    const AIMO_ACTION_GETCREDIT = 'getcredits.aspx?';
    const AIMO_ACTION_DELIVERYSTATUS = 'deliverystatus.aspx?';

    const STATUS_FAILED = 'failed';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
}