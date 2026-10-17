<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use WishboxRadicalMartLibrary\Service\Order\OrderService;
use WishboxRadicalMartRetailCrmLibrary\Repository\RetailCrmToRadicalMartStatusMappingRepository;
use WishboxRadicalMartRetailCrmLibrary\Service\RetailCrmToRadicalMartOrderStatusSyncService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers the RetailCRM-to-RadicalMart order status synchronization service.
 *
 * @since 1.0.0
 *
 * @noinspection PhpUnused
 */
final class RetailCrmToRadicalMartOrderStatusSyncServiceProvider implements ServiceProviderInterface
{
	/**
	 * Register the order status synchronization service.
	 *
	 * @param   Container  $container  Dependency injection container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function register(Container $container): void
	{
		$container->share(
			RetailCrmToRadicalMartOrderStatusSyncService::class,
			static fn (Container $container): RetailCrmToRadicalMartOrderStatusSyncService =>
				new RetailCrmToRadicalMartOrderStatusSyncService(
					$container->get(RetailCrmToRadicalMartStatusMappingRepository::class),
					$container->get(OrderService::class)
				)
		);
	}
}
