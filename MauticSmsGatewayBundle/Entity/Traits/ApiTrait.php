<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Entity\Traits;

use DOMDocument;
use MauticPlugin\MauticSmsGatewayBundle\Entity\SmsGatewaySettings as Settings;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces\ApiInterface;
use MauticPlugin\MauticSmsGatewayBundle\Helper\GsmEncoder;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Request;

trait ApiTrait
{
    /**
     * Authenticate user and set session id.
     * This method can be called only for Aimo provider.
     *
     * @param array $credentials
     * @return bool
     */
    public function auth(array $credentials)
    {
        $url = ApiInterface::AIMO_BASE_URL . ApiInterface::AIMO_ACTION_AUTH . http_build_query($credentials);

        if (isset($_SESSION['SMSGATEWAY']['AIMO']['SESSION']['ID']) && $_SESSION['SMSGATEWAY']['AIMO']['SESSION']['EXPIRED_AT'] > time()) {
            return true;
        } else {
            $response = $this->sendRequest($url);
            if ($response) {
                $responseToArray = explode(':', $response);

                $_SESSION['SMSGATEWAY']['AIMO']['SESSION']['ID'] = $responseToArray[1];
                $_SESSION['SMSGATEWAY']['AIMO']['SESSION']['EXPIRED_AT'] = strtotime("+20 min");
                $_SESSION['SMSGATEWAY']['AIMO']['SESSION']['CREATED_AT'] = time();

                return true;
            }

            return false;
        }
    }

    /**
     * @param $baseUrl
     * @param array $credentials
     * @param array $recipients
     * @return bool|string
     */
    public function sendMessage($baseUrl, array $credentials, array $recipients, $provider)
    {
        $url = $baseUrl . $this->prepareUrl($provider, $credentials, $recipients);

        return $this->sendRequest($url);
    }

    /**
     * Get credit of current user
     *
     * @param array $credentials
     * @param string $provider
     *
     * @return integer;
     * @throws \Exception
     */
    public function getCredit($credentials, $provider)
    {
        $url = $this->getBaseUrl($provider) . ApiInterface::AIMO_ACTION_GETCREDIT;
        if ($this->auth($credentials)) {
            $url .= 'session_id=' . $_SESSION['SMSGATEWAY']['AIMO']['SESSION']['ID'];
            $repsonse = $this->sendRequest($url);

            if (!empty($repsonse)) {
                return explode(':', $repsonse)[1];
            }

            throw new \Exception('Empty response from Aimo');
        }
    }

    /**
     * @param $provider
     */
    public function getStatuses($provider)
    {

    }

    /**
     * @param $url
     * @param string $method
     * @param array $postfields
     * @param array $headers
     * @return string|bool
     */
    public function sendRequest($url, $method = Request::METHOD_GET, $postfields = [], $headers = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $result = $this->validateResponse($response, $httpcode);

        curl_close($ch);

        return $result;
    }

    /**
     * @param $response
     * @param $httpcode
     * @return bool|mixed
     */
    public function validateResponse($response, $httpcode)
    {
        // If it's Aimo request
        try {
            if ($httpcode !== 200) {
                return false;
            }
        } catch (\Exception $e) {
            // No it's Engine
            if ($httpcode !== 200 || $httpcode !== 202) {
                return false;
            }
        }

        // If it's Engine than we'll create an array of data to save
        if ($isXml = $this->isXml($response)) {
            $response = $this->xmlToJson($isXml);
        }

        // Otherwise it's Aimo and we'll return it's string
        return $response;
    }

    /**
     * @param $string
     * @return bool
     */
    public function isJson($string) {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Check if it's xml
     *
     * @param string $string
     * @return array|bool
     */
    public function isXml($string)
    {
        libxml_use_internal_errors( true );

        $doc = new DOMDocument('1.0', 'utf-8');

        $doc->loadXML($string);

        $errors = libxml_get_errors();

        return (empty($errors)) ? $string : false;
    }

    /**
     * Convert xml to json
     *
     * @param string $xmlString
     * @return mixed
     */
    public function xmlToJson($xmlString)
    {
        $xml = new SimpleXMLElement($xmlString);
        return $this->xmlToArray($xml);
    }

    /**
     * @param $provider
     * @param array $credentials
     * @param array $recipients
     * @return string
     */
    public function prepareUrl($provider, array $credentials, array $recipients)
    {
        switch ($provider) {
            case ApiInterface::AIMO_NAME:
                $path = ApiInterface::AIMO_ACTION_SENDMSG;

                if (count($recipients) > 1) {
                    $credentials['to'] = implode(',', $recipients);
                } else {
                    $credentials['to'] = $recipients[0];
                }

                $path .= http_build_query($credentials);
                $path .= "&enc=utf8";
                break;
            case ApiInterface::ENGINE_NAME:
                $path = http_build_query($credentials);

                foreach ($recipients as $recipient) {
                    $path .= "&msisdn=$recipient";
                }
                break;
        }

        return $path;
    }

    /**
     * @param $text
     * @return string
     */
    public function prepareMessage($text)
    {
        return GsmEncoder::utf8_to_gsm0338($text);
    }

    /**
     * @param $xml
     * @param array $options
     * @return array
     */
    public function xmlToArray($xml, $options = array()) {
        $defaults = array(
            'namespaceSeparator' => ':',//you may want this to be something other than a colon
            'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
            'alwaysArray' => array(),   //array of xml tag names which should always become arrays
            'autoArray' => true,        //only create arrays for tags which appear more than once
            'textContent' => '$',       //key used for the text content of elements
            'autoText' => true,         //skip textContent key if node has no attributes or child nodes
            'keySearch' => false,       //optional search and replace on tag and attribute names
            'keyReplace' => false       //replace values for above search values (as passed to str_replace())
        );
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace

        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) $attributeName =
                    str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                $attributeKey = $options['attributePrefix']
                    . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                    . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }

        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = $this->xmlToArray($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);

                //replace characters in tag name
                if ($options['keySearch']) $childTagName =
                    str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                //add namespace prefix, if any
                if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;

                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                        in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                            ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }

        //get text content of node
        $textContentArray = array();
        $plainText = trim((string)$xml);
        if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;

        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
            ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        //return node as array
        return array(
            $xml->getName() => $propertiesArray
        );
    }

    /**
     * @param $tickets
     * @param $credentials
     * @return mixed
     */
    public function getStatusesAimo($tickets, $credentials)
    {
        if ($this->auth($credentials)) {
            $ticketIds = '';
            $ticketLength = count($tickets);
            $iterator = 1;
            foreach ($tickets as $ticket) {
                $ticketIds .= $ticket->getTicketId();

                if ($iterator != $ticketLength) {
                    $ticketIds .= ';';
                }

                $iterator++;
            }
            $params = [
                'session_id' => $_SESSION['SMSGATEWAY']['AIMO']['SESSION']['ID'],
                'ticket_id' => $ticketIds,
                'type' => 'api',
            ];

            $url = ApiInterface::AIMO_BASE_URL . ApiInterface::AIMO_ACTION_DELIVERYSTATUS;
            $url .= http_build_query($params);

            $response = explode("<br />", $this->sendRequest($url));

            foreach ($response as $statusString) {
                $statuses[] = explode(':', $statusString);
            }

            foreach ($tickets as $ticket) {
                $row = $statuses[array_search($ticket->getTicketId(), array_column($statuses, 2))];
                $ticket->setStatus($row[3]);
            }

            return $tickets;
        }
    }

    /**
     * @param $provider
     * @return string
     */
    public function getBaseUrl($provider)
    {
        $entity = $this->getEntityProvider($provider);

        switch (strtolower($entity->getProvider())) {
            case ApiInterface::AIMO_NAME:
                return ApiInterface::AIMO_BASE_URL;
                break;
            case ApiInterface::ENGINE_NAME:
                return $entity->getCallbackUrl() . '?';
                break;
        }
    }

    /**
     * @param $provider
     * @param $text
     * @return array
     */
    public function prepareData($provider, $text)
    {
        $entity = $this->getEntityProvider($provider);

        switch ($provider) {
            case ApiInterface::AIMO_NAME:
                $credentials = [
                    'client_id' => $entity->getClientId(),
                    'from' => $entity->getSender(),
                    'user' => $entity->getUsername(),
                    'pass' => $entity->getPassword(),
                    'text' => $this->prepareMessage($text),
                ];
                break;
            case ApiInterface::ENGINE_NAME:
                $credentials = [
                    'clientid' => $entity->getClientId(),
                    'sender' => $entity->getSender(),
                    'login' => $entity->getUsername(),
                    'password' => $entity->getPassword(),
                    'msg' => $this->prepareMessage($text),
                ];
                break;
        }

        return $credentials;
    }

    /**
     * @param $provider
     * @return mixed
     */
    public function getEntityProvider($provider)
    {
        $entity = $this->getDoctrine()
            ->getRepository(Settings::class)
            ->findOneBy([
                'provider' => $provider,
                'userId' => $this->getUser()->getId(),
            ]);

        return $entity;
    }
}