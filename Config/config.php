<?php
return [
	'name'        => 'Arena sms gateway',
	'description' => 'Plugin to send SMS to your clients',
	'author'      => 'SoftTeco',
	'version'     => '2.0.2',

	'routes'   => [
		'main' => [
			'plugin_smsgateway_settings'                 => [
				'path'       => '/smsgateway/settings',
				'controller' => 'MauticSmsGatewayBundle:Settings:index',
			],
			'plugin_smsgateway_settings_provider_create' => [
				'path'       => '/smsgateway/settings/provider/create',
				'controller' => 'MauticSmsGatewayBundle:Settings:create'
			],
			'plugin_smsgateway_settings_provider_store'  => [
				'path'       => '/smsgateway/settings/provider/store',
				'controller' => 'MauticSmsGatewayBundle:Settings:store',
				'method'     => 'POST',
			],
			'plugin_smsgateway_settings_provider_show'   => [
				'path'         => '/smsgateway/settings/provider/show/{providerId}',
				'controller'   => 'MauticSmsGatewayBundle:Settings:show',
				'requirements' => [
					'providerId' => '\d+',
				],
			],
			'plugin_smsgateway_settings_provider_edit'   => [
				'path'         => '/smsgateway/settings/provider/edit/{providerId}',
				'controller'   => 'MauticSmsGatewayBundle:Settings:edit',
				'requirements' => [
					'providerId' => '\d+',
				],
			],
			'plugin_smsgateway_settings_provider_update' => [
				'path'         => '/smsgateway/settings/provider/update/{providerId}',
				'controller'   => 'MauticSmsGatewayBundle:Settings:update',
				'method'       => 'POST',
				'requirements' => [
					'providerId' => '\d+',
				],
			],
			'plugin_smsgateway_settings_provider_delete' => [
				'path'         => '/smsgateway/settings/provider/delete/{providerId}',
				'controller'   => 'MauticSmsGatewayBundle:Settings:delete',
				'method'       => 'POST',
				'requirements' => [
					'providerId' => '\d+',
				],
			],
			'plugin_smsgateway_send_message_get'         => [
				'path'       => '/smsgateway/send',
				'controller' => 'MauticSmsGatewayBundle:Send:index',
				'method'     => 'GET',
			],
			'plugin_smsgateway_send_message_post'        => [
				'path'       => '/smsgateway/send',
				'controller' => 'MauticSmsGatewayBundle:Send:send',
				'method'     => 'POST',
			],
			'plugin_smsgateway_statuses'                 => [
				'path'       => '/smsgateway/statuses',
				'controller' => 'MauticSmsGatewayBundle:Status:index'
			],
			'plugin_smsgateway_statuses_update'          => [
				'path'       => '/smsgateway/statuses/update',
				'controller' => 'MauticSmsGatewayBundle:Status:update',
				'method'     => 'POST',
			],
		],
	],
	'services' => [
		'forms'        => [
			'plugin.smsgateway.form.settings' => [
				'class' => 'MauticPlugin\MauticSmsGatewayBundle\Form\Type\SettingsType',
				'alias' => 'smsgateway.form.settings',
			],
			'plugin.smsgateway.form.edit'     => [
				'class' => 'MauticPlugin\MauticSmsGatewayBundle\Form\Type\SettingsEditType',
				'alias' => 'smsgateway.form.edit',
			],
			'plugin.smsgateway.form.send'     => [
				'class' => 'MauticPlugin\MauticSmsGatewayBundle\Form\Type\SendSmsType',
				'alias' => 'smsgateway.form.send',
			],
		],
		'helpers'      => [
			'plugin.smsgateway.helper.gsm_encoder' => [
				'class' => 'MauticPlugin\MauticSmsGatewayBundle\Helper\GsmEncoder',
				'alias' => 'gsmencoder',
			],
		],
		'other'        => [
			'plugin.smsgateway.traits.api' => [
				'class' => \MauticPlugin\MauticSmsGatewayBundle\Entity\Traits\ApiTrait::class,
			],
			'mautic.sms.transport.arena'    => [
				'class'        => \MauticPlugin\MauticSmsGatewayBundle\Api\ArenaApi::class,
				'arguments'    => [
					'mautic.page.model.trackable',
					'mautic.helper.integration',
					'monolog.logger.mautic',
					'mautic.helper.user',
					'doctrine',
				],
				'tag'          => 'mautic.sms_transport',
				'tagArguments' => [
					'integrationAlias' => 'Arena',
				],
			],
		],
		'integrations' => [
			'plugin.smsgateway.integration.areana' => [
				'class' => \MauticPlugin\MauticSmsGatewayBundle\Integration\ArenaIntegration::class,
			],
		],
	],
	'menu'     => [
		'main'       => [
			'items' => [
				'mautic.sms.smses'                        => [
					'route'    => 'mautic_sms_index',
					'access'   => [ 'sms:smses:viewown', 'sms:smses:viewother' ],
					'parent'   => 'mautic.core.channels',
					'checks'   => [
						'integration' => [
							'Arena' => [
								'enabled' => true,
							],
						],
					],
					'priority' => 70,
				],
				'plugin.smsgateway.menu.parent'          => [
					'iconClass' => 'fa-envelope',
					'id'        => 'smsgateway-menu-parent',
					'priority'  => 70,
					'checks'   => [
						'integration' => [
							'Arena' => [
								'enabled' => true,
							],
						],
						'parameters' => [
							'sms_transport' => 'mautic.sms.transport.arena',
						],
					],
				],
				'plugin.smsgateway.menu.settings'        => [
					'route'  => 'plugin_smsgateway_settings',
					'id'     => 'smsgateway-menu-settings',
					'icon'   => 'fa-cog',
					'parent' => 'plugin.smsgateway.menu.parent',
				],
				'plugin.smsgateway.menu.provider.create' => [
					'route'  => 'plugin_smsgateway_settings_provider_create',
					'id'     => 'smsgateway-menu-provider-create',
					'icon'   => 'fa-plus',
					'parent' => 'plugin.smsgateway.menu.parent',
				],
				'plugin.smsgateway.menu.provider.send'   => [
					'route'  => 'plugin_smsgateway_send_message_get',
					'id'     => 'smsgateway-menu-provider-send',
					'icon'   => 'fa-paper-plane',
					'parent' => 'plugin.smsgateway.menu.parent',
				],
				'plugin.smsgateway.menu.statuses'        => [
					'route'  => 'plugin_smsgateway_statuses',
					'id'     => 'smsgateway-menu-statuses',
					'parent' => 'plugin.smsgateway.menu.parent',
				],
			],
		],
	],
	'parameters' => [
		'sms_transport' => 'mautic.sms.transport.arena',
	],
];