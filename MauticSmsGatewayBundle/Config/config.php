<?php
return [
	'name'        => 'Arena sms gateway',
	'description' => 'Plugin to send SMS to your clients',
	'author'      => 'SoftTeco',
	'version'     => '2.0.2',

	'routes'   => [
		'main' => [
			'plugin_smsgateaway_settings'                 => [
				'path'       => '/smsgateaway/settings',
				'controller' => 'MauticSmsGateawayBundle:Settings:index',
			],
			'plugin_smsgateaway_settings_provider_create' => [
				'path'       => '/smsgateaway/settings/provider/create',
				'controller' => 'MauticSmsGateawayBundle:Settings:create'
			],
			'plugin_smsgateaway_settings_provider_store'  => [
				'path'       => '/smsgateaway/settings/provider/store',
				'controller' => 'MauticSmsGateawayBundle:Settings:store',
				'method'     => 'POST',
			],
			'plugin_smsgateaway_settings_provider_show'   => [
				'path'         => '/smsgateaway/settings/provider/show/{providerId}',
				'controller'   => 'MauticSmsGateawayBundle:Settings:show',
				'requirements' => [
					'providerId' => '\d+',
				],
			],
			'plugin_smsgateaway_settings_provider_edit'   => [
				'path'         => '/smsgateaway/settings/provider/edit/{providerId}',
				'controller'   => 'MauticSmsGateawayBundle:Settings:edit',
				'requirements' => [
					'providerId' => '\d+',
				],
			],
			'plugin_smsgateaway_settings_provider_update' => [
				'path'         => '/smsgateaway/settings/provider/update/{providerId}',
				'controller'   => 'MauticSmsGateawayBundle:Settings:update',
				'method'       => 'POST',
				'requirements' => [
					'providerId' => '\d+',
				],
			],
			'plugin_smsgateaway_settings_provider_delete' => [
				'path'         => '/smsgateaway/settings/provider/delete/{providerId}',
				'controller'   => 'MauticSmsGateawayBundle:Settings:delete',
				'method'       => 'POST',
				'requirements' => [
					'providerId' => '\d+',
				],
			],
			'plugin_smsgateaway_send_message_get'         => [
				'path'       => '/smsgateaway/send',
				'controller' => 'MauticSmsGateawayBundle:Send:index',
				'method'     => 'GET',
			],
			'plugin_smsgateaway_send_message_post'        => [
				'path'       => '/smsgateaway/send',
				'controller' => 'MauticSmsGateawayBundle:Send:send',
				'method'     => 'POST',
			],
			'plugin_smsgateaway_statuses'                 => [
				'path'       => '/smsgateaway/statuses',
				'controller' => 'MauticSmsGateawayBundle:Status:index'
			],
			'plugin_smsgateaway_statuses_update'          => [
				'path'       => '/smsgateaway/statuses/update',
				'controller' => 'MauticSmsGateawayBundle:Status:update',
				'method'     => 'POST',
			],
		],
	],
	'services' => [
		'forms'        => [
			'plugin.smsgateaway.form.settings' => [
				'class' => 'MauticPlugin\MauticSmsGateawayBundle\Form\Type\SettingsType',
				'alias' => 'smsgateaway.form.settings',
			],
			'plugin.smsgateaway.form.edit'     => [
				'class' => 'MauticPlugin\MauticSmsGateawayBundle\Form\Type\SettingsEditType',
				'alias' => 'smsgateaway.form.edit',
			],
			'plugin.smsgateaway.form.send'     => [
				'class' => 'MauticPlugin\MauticSmsGateawayBundle\Form\Type\SendSmsType',
				'alias' => 'smsgateaway.form.send',
			],
		],
		'helpers'      => [
			'plugin.smsgateaway.helper.gsm_encoder' => [
				'class' => 'MauticPlugin\MauticSmsGateawayBundle\Helper\GsmEncoder',
				'alias' => 'gsmencoder',
			],
		],
		'other'        => [
			'plugin.smsgateaway.traits.api' => [
				'class' => \MauticPlugin\MauticSmsGateawayBundle\Entity\Traits\ApiTrait::class,
			],
			'mautic.sms.transport.arena'    => [
				'class'        => \MauticPlugin\MauticSmsGateawayBundle\Api\ArenaApi::class,
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
			'plugin.smsgateaway.integration.areana' => [
				'class' => \MauticPlugin\MauticSmsGateawayBundle\Integration\ArenaIntegration::class,
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
				'plugin.smsgateaway.menu.parent'          => [
					'iconClass' => 'fa-envelope',
					'id'        => 'smsgateaway-menu-parent',
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
				'plugin.smsgateaway.menu.settings'        => [
					'route'  => 'plugin_smsgateaway_settings',
					'id'     => 'smsgateaway-menu-settings',
					'icon'   => 'fa-cog',
					'parent' => 'plugin.smsgateaway.menu.parent',
				],
				'plugin.smsgateaway.menu.provider.create' => [
					'route'  => 'plugin_smsgateaway_settings_provider_create',
					'id'     => 'smsgateaway-menu-provider-create',
					'icon'   => 'fa-plus',
					'parent' => 'plugin.smsgateaway.menu.parent',
				],
				'plugin.smsgateaway.menu.provider.send'   => [
					'route'  => 'plugin_smsgateaway_send_message_get',
					'id'     => 'smsgateaway-menu-provider-send',
					'icon'   => 'fa-paper-plane',
					'parent' => 'plugin.smsgateaway.menu.parent',
				],
				'plugin.smsgateaway.menu.statuses'        => [
					'route'  => 'plugin_smsgateaway_statuses',
					'id'     => 'smsgateaway-menu-statuses',
					'parent' => 'plugin.smsgateaway.menu.parent',
				],
			],
		],
	],
	'parameters' => [
		'sms_transport' => 'mautic.sms.transport.arena',
	],
];