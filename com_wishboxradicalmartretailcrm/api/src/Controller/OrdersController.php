<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace Joomla\Component\WishboxRadicalMartRetailCrm\Api\Controller;

use Error;
use Exception;
use InvalidArgumentException;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\ApiController;
use Joomla\Component\WishboxRadicalMartRetailCrm\Administrator\Extension\WishboxRadicalMartRetailCrmComponent;
use JsonException;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Handles RetailCRM order callbacks.
 *
 * @since        1.0.0
 *
 * @noinspection PhpUnused
 */
final class OrdersController extends ApiController
{
	/**
	 * Publish a tracking number update request.
	 *
	 * @return self
	 *
	 * @throws Error
	 * @throws Exception
	 * @throws InvalidArgumentException
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function updateTrackNumber(): self
	{
		try
		{
			$payload        = $this->getJsonPayload();
			$orderId        = (int) ($payload['order_id'] ?? 0);
			$trackingNumber = trim((string) ($payload['track_number'] ?? ''));

			if ($orderId <= 0)
			{
				throw new InvalidArgumentException('order_id must be greater than zero.', 400);
			}

			if ($trackingNumber === '')
			{
				throw new InvalidArgumentException('track_number is required.', 400);
			}

			$app = $this->app;

			/** @var WishboxRadicalMartRetailCrmComponent $wishboxRadicalMartRetailCrmComponent */
			$wishboxRadicalMartRetailCrmComponent = $app->bootComponent('com_wishboxradicalmartretailcrm');

			$orderService = $wishboxRadicalMartRetailCrmComponent->getOrderService();

			$orderService->setShippingDataValue($orderId, 'tracking_number', $trackingNumber);
		}
		catch (Exception|Error $e)
		{
			$message = (string) $e;
			Log::add($message, Log::ERROR, 'wishboxradicalmartretailcrm');
			throw $e;
		}

		return $this;
	}

	/**
	 * Decode the JSON request object.
	 *
	 * @return array<string, mixed>
	 *
	 * @throws InvalidArgumentException
	 *
	 * @since 1.0.0
	 */
	private function getJsonPayload(): array
	{
		try
		{
			$requestBody = $this->input->json
				->getRaw();

			$payload = json_decode($requestBody, true, 512, JSON_THROW_ON_ERROR);
		}
		catch (JsonException $exception)
		{
			throw new InvalidArgumentException('The request body must contain valid JSON.', 400, $exception);
		}

		if (!is_array($payload))
		{
			throw new InvalidArgumentException('The request body must contain a JSON object.', 400);
		}

		return $payload;
	}

	/**
	 * Publish a RadicalMart order status update request.
	 *
	 * @param   int|null  $radicalmartOrderId  RadicalMart order identifier.
	 *
	 * @return self
	 *
	 * @throws Error
	 * @throws Exception
	 * @throws InvalidArgumentException
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function updateStatus(?int $radicalmartOrderId = null): self
	{
		if ($radicalmartOrderId === null)
		{
			$radicalmartOrderId = $this->input->getInt('radicalmart_order_id');
		}

		if ($radicalmartOrderId <= 0)
		{
			throw new InvalidArgumentException('radicalmart_order_id must be greater than zero.', 400);
		}

		try
		{
			$payload                  = $this->getJsonPayload();
			$retailCrmOrderStatusCode = trim((string) ($payload['retailcrmorderstatuscode'] ?? ''));
			$app                      = $this->app;

			/** @var WishboxRadicalMartRetailCrmComponent $wishboxRadicalMartRetailCrmComponent */
			$wishboxRadicalMartRetailCrmComponent = $app->bootComponent('com_wishboxradicalmartretailcrm');

			$orderStatusSyncService =
				$wishboxRadicalMartRetailCrmComponent->getRetailCrmToRadicalMartOrderStatusSyncService();

			$orderStatusSyncService->updateStatus($radicalmartOrderId, $retailCrmOrderStatusCode);
		}
		catch (Exception|Error $e)
		{
			$message = (string) $e;
			Log::add($message, Log::ERROR, 'radicalmartretailcrm');
			throw $e;
		}

		return $this;
	}
}
