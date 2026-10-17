<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace Joomla\Component\WishboxRadicalMartRetailCrm\Api\Controller;

use InvalidArgumentException;
use Joomla\CMS\Application\ApiApplication;
use Joomla\CMS\Log\Log;
use Joomla\Component\WishboxRadicalMartRetailCrm\Administrator\Extension\WishboxRadicalMartRetailCrmComponent;
use JsonException;
use Throwable;
use WishboxRetailCrmLibrary\Controller\RetailCrmApiController;

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
final class OrdersController extends RetailCrmApiController
{
	/**
	 * Publish a tracking number update request.
	 *
	 * @return void
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function updateTrackNumber(): void
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

			/** @var ApiApplication $app */
			$app = $this->app;

			/** @var WishboxRadicalMartRetailCrmComponent $wishboxRadicalMartRetailCrmComponent */
			$wishboxRadicalMartRetailCrmComponent = $app->bootComponent('com_wishboxradicalmartretailcrm');

			$orderService = $wishboxRadicalMartRetailCrmComponent->getOrderService();

			$orderService->setShippingDataValue($orderId, 'tracking_number', $trackingNumber);
		}
		catch (InvalidArgumentException $invalidArgumentException)
		{
			$this->setRetailCrmResponse(
				[
					'success'  => false,
					'errorMsg' => $invalidArgumentException->getMessage(),
				],
				400
			);

			return;
		}
		catch (Throwable $throwable)
		{
			Log::add((string) $throwable, Log::ERROR, 'com_wishboxradicalmartretailcrm');
			$this->setRetailCrmResponse(
				[
					'success'  => false,
					'errorMsg' => 'Unable to update the tracking number.',
				],
				500
			);

			return;
		}

		$this->setRetailCrmResponse(['success' => true], 200);
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
			$requestBody = $this->input->json->getRaw();

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
	 * @return void
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function updateStatus(): void
	{
		try
		{
			$radicalmartOrderId = $this->input->getInt('radicalmart_order_id');

			if ($radicalmartOrderId <= 0)
			{
				throw new InvalidArgumentException('radicalmart_order_id must be greater than zero.', 400);
			}

			$payload = $this->getJsonPayload();
			$retailCrmOrderStatusCode = trim((string) ($payload['retailcrmorderstatuscode'] ?? ''));

			/** @var ApiApplication $app */
			$app = $this->app;

			/** @var WishboxRadicalMartRetailCrmComponent $wishboxRadicalMartRetailCrmComponent */
			$wishboxRadicalMartRetailCrmComponent = $app->bootComponent('com_wishboxradicalmartretailcrm');

			$orderStatusSyncService =
				$wishboxRadicalMartRetailCrmComponent->getRetailCrmToRadicalMartOrderStatusSyncService();

			$orderStatusSyncService->updateStatus($radicalmartOrderId, $retailCrmOrderStatusCode);
		}
		catch (InvalidArgumentException $invalidArgumentException)
		{
			$this->setRetailCrmResponse(
				[
					'success'  => false,
					'errorMsg' => $invalidArgumentException->getMessage(),
				],
				400
			);

			return;
		}
		catch (Throwable $throwable)
		{
			Log::add((string) $throwable, Log::ERROR, 'com_wishboxradicalmartretailcrm');
			$this->setRetailCrmResponse(
				[
					'success'  => false,
					'errorMsg' => 'Unable to update the RadicalMart order status.',
				],
				500
			);

			return;
		}

		$this->setRetailCrmResponse(['success' => true], 200);
	}
}
