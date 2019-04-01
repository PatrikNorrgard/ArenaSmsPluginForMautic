<?php

namespace MauticPlugin\MauticSmsGatewayBundle\Api;

use Doctrine\Common\Persistence\ManagerRegistry;
use Mautic\CoreBundle\Helper\UserHelper;
use MauticPlugin\MauticSmsGatewayBundle\Entity\SmsGatewaySettings;
use Mautic\PageBundle\Model\TrackableModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use \Mautic\SmsBundle\Api\AbstractSmsApi;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Interfaces\ApiInterface;
use MauticPlugin\MauticSmsGatewayBundle\Entity\Traits\ApiTrait;
use Monolog\Logger;
use Mautic\LeadBundle\Entity\Lead;

class ArenaApi extends AbstractSmsApi {
	use ApiTrait;
	/** @var Logger */
	protected $logger;

	/** @var UserHelper $user */
	protected $user;

	/** @var ManagerRegistry $doctrine */
	protected $doctrine;

	/** @var array $params */
	protected $params = [];

	public function __construct( TrackableModel $pageTrackableModel, IntegrationHelper $integrationHelper, Logger $logger, UserHelper $user, ManagerRegistry $doctrine ) {
		$this->logger   = $logger;
		$this->user     = $user->getUser();
		$this->doctrine = $doctrine;
		$integration    = $integrationHelper->getIntegrationObject( 'Arena' );
		$features       = $integration->getIntegrationSettings()->getFeatureSettings();

		if ( $integration && $integration->getIntegrationSettings()->getIsPublished() ) {
			$this->params['provider'] = $features['provider'];
			$this->params['endpoint'] = $features['callback_url'];
			$this->params['user_id']  = $features['user_id'];
		}

		parent::__construct( $pageTrackableModel );
	}

	/**
	 * @param Lead $lead
	 * @param string $content
	 *
	 * @return mixed|void
	 * @throws \Exception
	 */
	public function sendSms( Lead $lead, $content ) {
		$this->params['phones'][] = $lead->getMobile();
		$this->params['messages'] = $content;
		// Getting provider entity
		/** @var SmsGatewaySettings $provider */
		$provider = $this->getCurrentProvider();

		$credentials = $this->prepareData( $provider, $this->params['messages'] );
		$baseUrl     = $this->getBaseUrl( $provider );

		if ( ! empty( $credentials['text'] ) || ! empty( $credentials['msg'] ) ) {
			$this->sendMessage( $baseUrl, $credentials, $this->params['phones'], $this->params['provider'] );
		} else {
			throw new \Exception( 'Empty message' );
		}
	}

	/**
	 * @return UserHelper
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	public function getProvider() {
		return $this->params['provider'];
	}

	public function getEndpoint() {
		return $this->params['endpoint'];
	}

	/**
	 * Gets provider
	 *
	 * @param string $provider
	 *
	 * @return mixed
	 */
	private function getCurrentProvider() {
		if ( is_null( $this->params['endpoint'] ) ) {
			$this->params['endpoint'] = '';
		}

		return $this->doctrine
			->getRepository( SmsGatewaySettings::class )
			->findOneBy( [
				'provider'    => $this->params['provider'],
				'userId'      => $this->params['user_id'],
				'callbackUrl' => $this->params['endpoint'],
			] );
	}

	/**
	 * @param SmsGatewaySettings $provider
	 * @param $text
	 *
	 * @return array
	 */
	public function prepareData( SmsGatewaySettings $provider, $text ) {
		switch ( $provider->getProvider() ) {
			case ApiInterface::AIMO_NAME:
				$credentials = [
					'client_id' => $provider->getClientId(),
					'from'      => $provider->getSender(),
					'user'      => $provider->getUsername(),
					'pass'      => $provider->getPassword(),
					'text'      => $text,
				];
				$this->writeToFile( 'SMS Content', $credentials['text'] );
				break;
			case ApiInterface::ENGINE_NAME:
				$credentials = [
					'clientid' => $provider->getClientId(),
					'sender'   => $provider->getSender(),
					'login'    => $provider->getUsername(),
					'password' => $provider->getPassword(),
					'msg'      => $text,
				];
				$this->writeToFile( 'SMS Content', $credentials['msg'] );
				break;
		}

		return $credentials;
	}

	public function getBaseUrl( SmsGatewaySettings $provider ) {
		switch ( strtolower( $provider->getProvider() ) ) {
			case ApiInterface::AIMO_NAME:
				return ApiInterface::AIMO_BASE_URL;
				break;
			case ApiInterface::ENGINE_NAME:
				return $provider->getCallbackUrl() . '?';
				break;
		}
	}

	public function writeToFile( $message, $data ) {
		$file = __DIR__ . '/../../../app/logs/arenaapi.log';
		$data = "[" . date( 'H:i:s d-m-Y' ) . "] $message: " . json_encode( $data ) . PHP_EOL;
		file_put_contents( $file, $data, FILE_APPEND );
	}
}
