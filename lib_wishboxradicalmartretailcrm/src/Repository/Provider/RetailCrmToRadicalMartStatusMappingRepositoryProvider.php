<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Repository\Provider;

use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use WishboxRadicalMartRetailCrmLibrary\Repository\RetailCrmToRadicalMartStatusMappingRepository;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers the RetailCRM-to-RadicalMart status mapping repository.
 *
 * @since 1.0.0
 *
 * @noinspection PhpUnused
 */
final class RetailCrmToRadicalMartStatusMappingRepositoryProvider implements ServiceProviderInterface
{
	/**
	 * Register the status mapping repository.
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
			RetailCrmToRadicalMartStatusMappingRepository::class,
			static fn (Container $container): RetailCrmToRadicalMartStatusMappingRepository =>
				new RetailCrmToRadicalMartStatusMappingRepository(
					$container->get(DatabaseInterface::class)
				)
		);
	}
}
