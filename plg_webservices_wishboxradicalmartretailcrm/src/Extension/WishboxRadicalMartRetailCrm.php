<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace Joomla\Plugin\Webservices\WishboxRadicalMartRetailCrm\Extension;

use Joomla\CMS\Event\Application\BeforeApiRouteEvent;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers API routes for the RadicalMart RetailCRM bridge.
 *
 * @since 1.0.0
 */
final class WishboxRadicalMartRetailCrm extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Return the subscribed Joomla events.
	 *
	 * @return array<string, string>
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public static function getSubscribedEvents(): array
	{
		return ['onBeforeApiRoute' => 'onBeforeApiRoute'];
	}

	/**
	 * Register the RadicalMart RetailCRM API routes.
	 *
	 * @param   BeforeApiRouteEvent  $event  API routing event.
	 *
	 * @return void
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function onBeforeApiRoute(BeforeApiRouteEvent $event): void
	{
		$routes = [
			new Route(
				['PATCH'],
				'v1/wishboxradicalmartretailcrm/updateshippingnumber',
				'wishboxradicalmartretailcrm.updateShippingNumber',
				[],
				['component' => 'com_wishboxradicalmartretailcrm']
			),
			new Route(
				['PATCH'],
				'v1/wishboxradicalmartretailcrm/updatetracknumber',
				'wishboxradicalmartretailcrm.updateTrackNumber',
				[],
				['component' => 'com_wishboxradicalmartretailcrm']
			),
			new Route(
				['PATCH'],
				'v1/wishboxradicalmartretailcrm/order/:radicalmart_order_id/updatestatus',
				'wishboxradicalmartretailcrm.updateStatus',
				['radicalmart_order_id' => '(\\d+)'],
				['component' => 'com_wishboxradicalmartretailcrm']
			),
			new Route(
				['POST'],
				'v1/wishboxradicalmartretailcrm/products/updatequantityfromorder/:retailcrm_order_id',
				'wishboxradicalmartretailcrm.productsUpdateQuantityFromOrder',
				['retailcrm_order_id' => '(\\d+)'],
				['component' => 'com_wishboxradicalmartretailcrm']
			),
			new Route(
				['POST'],
				'v1/wishboxradicalmartretailcrm/products/updatequantity',
				'wishboxradicalmartretailcrm.productsUpdateQuantity',
				[],
				['component' => 'com_wishboxradicalmartretailcrm']
			),
			new Route(
				['PATCH'],
				'v1/wishboxradicalmartretailcrm/orders/:retailcrm_order_id/updatesumtowords',
				'wishboxradicalmartretailcrm.ordersUpdateSumToWords',
				['retailcrm_order_id' => '(\\d+)'],
				['component' => 'com_wishboxradicalmartretailcrm']
			),
			new Route(
				['PATCH'],
				'v1/wishboxradicalmartretailcrm/orders/:retailcrm_order_id/createpaymentlink',
				'wishboxradicalmartretailcrm.ordersCreatePaymentLink',
				['retailcrm_order_id' => '(\\d+)'],
				['component' => 'com_wishboxradicalmartretailcrm']
			),
			new Route(
				['PATCH'],
				'v1/wishboxradicalmartretailcrm/orders/:retailcrm_order_id/createrussianpostshippingorder',
				'wishboxradicalmartretailcrm.ordersCreateRussianPostShippingOrder',
				['retailcrm_order_id' => '(\\d+)'],
				['component' => 'com_wishboxradicalmartretailcrm']
			),
		];
		$router = $event->getRouter();
		$router->addRoutes($routes);
	}
}
