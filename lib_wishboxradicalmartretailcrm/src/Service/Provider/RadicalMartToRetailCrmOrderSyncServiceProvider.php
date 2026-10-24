<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use RetailCrm\Api\Client;
use WishboxRadicalMartRetailCrmLibrary\Service\RadicalMartToRetailCrmOrderSyncService;
use WishboxRetailCrmLibrary\Configuration\RetailCrmConfiguration;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers the RadicalMart-to-RetailCRM order synchronization service.
 *
 * @since 1.0.0
 */
final class RadicalMartToRetailCrmOrderSyncServiceProvider implements ServiceProviderInterface
{
	/**
	 * Register the order synchronization service.
	 *
	 * @param   Container  $container  Dependency injection container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function register(Container $container): void
	{
		$container->share(
			RadicalMartToRetailCrmOrderSyncService::class,
			static fn (Container $container): RadicalMartToRetailCrmOrderSyncService =>
				new RadicalMartToRetailCrmOrderSyncService(
					$container->get(Client::class),
					$container->get(RetailCrmConfiguration::class)
				)
		);
	}
}
