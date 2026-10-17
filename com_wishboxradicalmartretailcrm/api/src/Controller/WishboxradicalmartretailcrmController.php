<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace Joomla\Component\WishboxRadicalMartRetailCrm\Api\Controller;

use Error;
use Exception;
use InvalidArgumentException;
use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\ApiController;
use Joomla\Component\WishboxRadicalMartRetailCrm\Administrator\Extension\WishboxRadicalMartRetailCrmComponent;
use JsonException;
use RuntimeException;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Accepts RetailCRM callbacks intended for RadicalMart.
 *
 * @since        1.0.0
 *
 * @noinspection PhpUnused
 */
final class WishboxradicalmartretailcrmController extends ApiController
{
	/**
	 * Publish a request to update the order sum in words.
	 *
	 * @return self
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function ordersUpdateSumToWords(): self
	{
		$payload = [
			'retailcrm_order_id' => $this->getPositiveRouteId('retailcrm_order_id'),
		];
		$this->dispatchOperation('onWishboxRadicalMartRetailCrmUpdateOrderSumToWords', $payload);

		return $this;
	}

	/**
	 * Read a positive integer route variable.
	 *
	 * @param   string  $name  Route variable name.
	 *
	 * @return integer
	 *
	 * @since 1.0.0
	 */
	private function getPositiveRouteId(string $name): int
	{
		$value = $this->input->getInt($name);

		if ($value <= 0)
		{
			throw new InvalidArgumentException($name . ' must be greater than zero.', 400);
		}

		return $value;
	}

	/**
	 * Dispatch a validated integration operation.
	 *
	 * @param   string                $eventName  Joomla event name.
	 * @param   array<string, mixed>  $payload    Validated operation payload.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	private function dispatchOperation(string $eventName, array $payload): void
	{
		$event      = new GenericEvent(
			$eventName,
			[
				'subject' => $this,
				'payload' => $payload,
				'handled' => false,
			]
		);
		$dispatcher = $this->app->getDispatcher();
		$dispatcher->dispatch($eventName, $event);

		if ($event->getArgument('handled', false) !== true)
		{
			throw new RuntimeException('No RadicalMart integration handler processed this request.', 501);
		}
	}
}
