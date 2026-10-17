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
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use WishboxRadicalMartLibrary\Service\Order\OrderService;
use WishboxRadicalMartLibrary\Service\Provider\OrderServiceProvider;
use WishboxRadicalMartLibrary\Service\Provider\ProductServiceProvider;
use WishboxRadicalMartRetailCrmLibrary\Repository\Provider\RetailCrmToRadicalMartStatusMappingRepositoryProvider;
use WishboxRadicalMartRetailCrmLibrary\Service\Provider\RetailCrmToRadicalMartOrderStatusSyncServiceProvider;
use WishboxRadicalMartRetailCrmLibrary\Service\Provider\WishboxRadicalMartRetailCrmProductServiceProvider;
use WishboxRadicalMartRetailCrmLibrary\Service\RetailCrmToRadicalMartOrderStatusSyncService;
use WishboxRadicalMartRetailCrmLibrary\Service\WishboxRadicalMartRetailCrmProductService;
use WishboxRetailCrmLibrary\Service\Provider\RetailCrmServiceProvider;
use WishboxRetailCrmLibrary\Service\Provider\RetailCrmOrderServiceProvider;

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

		if (!class_exists(ProductServiceProvider::class) || !class_exists(OrderServiceProvider::class))
		{
			throw new RuntimeException('The WishBox RadicalMart library must be installed before using this component.');
		}

		$container->registerServiceProvider(new RetailCrmServiceProvider());
		$container->registerServiceProvider(new RetailCrmOrderServiceProvider());
		$container->registerServiceProvider(new OrderServiceProvider());
		$container->registerServiceProvider(new ProductServiceProvider());
		$container->registerServiceProvider(new RetailCrmToRadicalMartStatusMappingRepositoryProvider());
		$container->registerServiceProvider(new RetailCrmToRadicalMartOrderStatusSyncServiceProvider());
		$container->registerServiceProvider(new WishboxRadicalMartRetailCrmProductServiceProvider());

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
