<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use RetailCrm\Api\Client;
use WishboxRadicalMartLibrary\Service\Product\ProductService;
use WishboxRadicalMartRetailCrmLibrary\Service\WishboxRadicalMartRetailCrmProductService;
use WishboxRetailCrmLibrary\Service\RetailCrmOrderService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers the RadicalMart-RetailCRM product service.
 *
 * @since 1.0.0
 *
 * @noinspection PhpUnused
 */
final class WishboxRadicalMartRetailCrmProductServiceProvider implements ServiceProviderInterface
{
	/**
	 * Register the product service.
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
			WishboxRadicalMartRetailCrmProductService::class,
			static fn (Container $container): WishboxRadicalMartRetailCrmProductService =>
				new WishboxRadicalMartRetailCrmProductService(
					$container->get(Client::class),
					$container->get(ProductService::class),
					$container->get(RetailCrmOrderService::class)
				)
		);
	}
}
