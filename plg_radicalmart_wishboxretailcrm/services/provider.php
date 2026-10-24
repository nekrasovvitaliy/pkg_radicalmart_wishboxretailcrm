<?php
/**
 * @copyright  (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license        GNU General Public License version 2 or later
 */

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\RadicalMart\WishboxRetailCrm\Extension\WishboxRetailCrm;
use WishboxRadicalMartRetailCrmLibrary\Service\Provider\RadicalMartToRetailCrmOrderSyncServiceProvider;
use WishboxRetailCrmLibrary\Service\Provider\RetailCrmServiceProvider;

defined('_JEXEC') or die;

/**
 * Registers the RadicalMart Wishbox RetailCRM plugin.
 *
 * @since 1.0.0
 */
return new class implements ServiceProviderInterface {
	/**
	 * Register the plugin and its services.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function register(Container $container): void
	{
		if (!class_exists(RetailCrmServiceProvider::class))
		{
			throw new RuntimeException('The WishBox RetailCRM library must be installed before using this plugin.');
		}

		if (!class_exists(RadicalMartToRetailCrmOrderSyncServiceProvider::class))
		{
			throw new RuntimeException(
				'The WishBox RadicalMart RetailCRM library must be installed before using this plugin.'
			);
		}

		$container->registerServiceProvider(new RetailCrmServiceProvider());
		$container->registerServiceProvider(new RadicalMartToRetailCrmOrderSyncServiceProvider());

		$container->set(
			PluginInterface::class,
			static function (Container $container): PluginInterface {
				$dispatcher = $container->get(DispatcherInterface::class);
				$config = (array) PluginHelper::getPlugin('radicalmart', 'wishboxretailcrm');
				$wishboxRetailCrmPlugin = new WishboxRetailCrm($dispatcher, $config);
				$wishboxRetailCrmPlugin->setApplication(Factory::getApplication());
				$wishboxRetailCrmPlugin->setContainer($container);

				return $wishboxRetailCrmPlugin;
			}
		);
	}
};
