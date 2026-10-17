<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\WishboxRadicalMartRetailCrm\Administrator\Extension\WishboxRadicalMartRetailCrmComponent;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use RetailCrm\Api\Client;
use WishboxRadicalMartLibrary\Service\Order\OrderService;
use WishboxRadicalMartLibrary\Service\Provider\OrderServiceProvider;
use WishboxRadicalMartLibrary\Service\Product\ProductService;
use WishboxRadicalMartRetailCrmLibrary\Repository\RetailCrmToRadicalMartStatusMappingRepository;
use WishboxRadicalMartRetailCrmLibrary\Service\RetailCrmToRadicalMartOrderStatusSyncService;
use WishboxRadicalMartRetailCrmLibrary\Service\WishboxRadicalMartRetailCrmProductService;
use WishboxRetailCrmLibrary\Service\Provider\RetailCrmServiceProvider;
use WishboxRetailCrmLibrary\Service\Provider\RetailCrmOrderServiceProvider;
use WishboxRetailCrmLibrary\Service\RetailCrmOrderService;

defined('_JEXEC') or die;

/**
 * Registers the component services.
 *
 * @since 1.0.0
 */
return new class implements ServiceProviderInterface {
	/**
	 * Register the component in the Joomla container.
	 *
	 * @param   Container  $container  Dependency injection container.
	 *
	 * @return void
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function register(Container $container): void
	{
		if (!class_exists(RetailCrmServiceProvider::class) || !class_exists(RetailCrmOrderServiceProvider::class))
		{
			throw new RuntimeException('The WishBox RetailCRM library must be installed before using this component.');
		}

		if (!class_exists(ProductService::class) || !class_exists(OrderServiceProvider::class))
		{
			throw new RuntimeException('The WishBox RadicalMart library must be installed before using this component.');
		}

		$container->registerServiceProvider(new RetailCrmServiceProvider());
		$container->registerServiceProvider(new RetailCrmOrderServiceProvider());
		$container->registerServiceProvider(new OrderServiceProvider());
		$container->share(
			ProductService::class,
			static fn (): ProductService => new ProductService()
		);
		$container->share(
			RetailCrmToRadicalMartStatusMappingRepository::class,
			static fn (Container $container): RetailCrmToRadicalMartStatusMappingRepository =>
				new RetailCrmToRadicalMartStatusMappingRepository(
					$container->get(DatabaseInterface::class)
				)
		);
		$container->share(
			RetailCrmToRadicalMartOrderStatusSyncService::class,
			static fn (Container $container): RetailCrmToRadicalMartOrderStatusSyncService =>
				new RetailCrmToRadicalMartOrderStatusSyncService(
					$container->get(RetailCrmToRadicalMartStatusMappingRepository::class),
					$container->get(OrderService::class)
				)
		);

		$container->share(
			WishboxRadicalMartRetailCrmProductService::class,
			static fn (Container $container): WishboxRadicalMartRetailCrmProductService =>
				new WishboxRadicalMartRetailCrmProductService(
					$container->get(Client::class),
					$container->get(ProductService::class),
					$container->get(RetailCrmOrderService::class)
				)
		);

		$container->registerServiceProvider(
			new MVCFactory('\\Joomla\\Component\\WishboxRadicalMartRetailCrm')
		);
		$container->registerServiceProvider(
			new ComponentDispatcherFactory('\\Joomla\\Component\\WishboxRadicalMartRetailCrm')
		);

		$container->set(
			ComponentInterface::class,
			static function (Container $container): ComponentInterface {
				$wishboxRadicalMartRetailCrmComponent = new WishboxRadicalMartRetailCrmComponent(
					$container->get(ComponentDispatcherFactoryInterface::class)
				);
				$wishboxRadicalMartRetailCrmComponent->setMVCFactory(
					$container->get(MVCFactoryInterface::class)
				);
				$wishboxRadicalMartRetailCrmComponent->setOrderService(
					$container->get(OrderService::class)
				);
				$wishboxRadicalMartRetailCrmComponent->setRetailCrmToRadicalMartOrderStatusSyncService(
					$container->get(RetailCrmToRadicalMartOrderStatusSyncService::class)
				);
				$wishboxRadicalMartRetailCrmComponent->setWishboxRadicalMartRetailCrmProductService(
					$container->get(WishboxRadicalMartRetailCrmProductService::class)
				);

				return $wishboxRadicalMartRetailCrmComponent;
			}
		);
	}
};
