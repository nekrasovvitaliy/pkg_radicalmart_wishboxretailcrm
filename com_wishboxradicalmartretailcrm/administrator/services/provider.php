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
use RetailCrm\Api\Client;
use WishboxRadicalMartLibrary\Service\Product\ProductService;
use WishboxRadicalMartRetailCrmLibrary\Service\WishboxRadicalMartRetailCrmProductService;
use WishboxRetailCrmLibrary\Service\Provider\RetailCrmServiceProvider;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

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
		if (!class_exists(RetailCrmServiceProvider::class))
		{
			throw new RuntimeException('The WishBox RetailCRM library must be installed before using this component.');
		}

		if (!class_exists(ProductService::class))
		{
			throw new RuntimeException('The WishBox RadicalMart library must be installed before using this component.');
		}

		$container->registerServiceProvider(new RetailCrmServiceProvider());
		$container->share(
			ProductService::class,
			static fn(): ProductService => new ProductService()
		);

		$container->share(
			WishboxRadicalMartRetailCrmProductService::class,
			static fn(Container $container): WishboxRadicalMartRetailCrmProductService =>
				new WishboxRadicalMartRetailCrmProductService(
					$container->get(Client::class),
					$container->get(ProductService::class)
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
				$wishboxRadicalMartRetailCrmComponent->setWishboxRadicalMartRetailCrmProductService(
					$container->get(WishboxRadicalMartRetailCrmProductService::class)
				);

				return $wishboxRadicalMartRetailCrmComponent;
			}
		);
	}
};
