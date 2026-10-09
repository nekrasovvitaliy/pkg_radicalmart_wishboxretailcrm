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
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\ApiController;
use Joomla\Component\WishboxRadicalMartRetailCrm\Administrator\Extension\WishboxRadicalMartRetailCrmComponent;
use Joomla\Component\RadicalMart\Administrator\Extension\RadicalMartComponent;
use Joomla\Component\RadicalMart\Administrator\Model\OrderModel;
use Joomla\Component\RadicalMart\Administrator\Table\OrderTable;
use Joomla\Component\RadicalMart\Administrator\Table\StatusTable;
use JsonException;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Interfaces\ClientExceptionInterface;
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
	 * Publish a tracking number update request.
	 *
	 * @return self
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function updateTrackNumber(): self
	{
		$payload = $this->getJsonPayload();
		$this->validateTrackingPayload($payload);
		$this->dispatchOperation('onWishboxRadicalMartRetailCrmUpdateTrackNumber', $payload);

		return $this;
	}

	/**
	 * Publish a RadicalMart order status update request.
	 *
	 * @param   int|null  $radicalmartorderId
	 *
	 * @return self
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function updateStatus(?int $radicalmartorderId = null): self
	{
		if ($radicalmartorderId === null)
		{
			$radicalmartorderId = $this->input->get('$radicalmartorder_id', 0, 'int');
		}

		try
		{
			$data = $this->input->get(
				'data',
				json_decode($this->input->json->getRaw(), true),
				'array'
			);

			$retailCrmOrderStatusCode = $data['retailcrmorderstatuscode'];

			$app = Factory::getApplication();

			/** @var RadicalMartComponent $radicalMartComponent */
			$radicalMartComponent = $app->bootComponent('com_radicalmart');

			/** @var StatusTable $statusTable */
			$statusTable = $radicalMartComponent->getMVCFactory()
				->createTable(
					'Status',
					'Administrator'
				);

			$statusTable->load(['retailcrm_character_code' => $retailCrmOrderStatusCode]);


			/** @var OrderTable $orderTable */
			$orderTable = $radicalMartComponent->getMVCFactory()
				->createTable('Order', 'Administrator');

			if (!$orderTable->load($radicalmartorderId))
			{
				throw new Exception('RadicalMart order with id "' . $radicalmartorderId . '" was not found.', 404);
			}

			$oldOrderStatusId = (int) $orderTable->status;
			$newOrderStatusId = $statusTable->id;

			if ($newOrderStatusId && $newOrderStatusId !== $oldOrderStatusId)
			{

				/** @var OrderModel $orderModel */
				$orderModel = $radicalMartComponent->getMVCFactory()
					->createModel('order', 'Administrator', ['ignore_request' => true]);

				$orderModel->updateStatus(
					$radicalmartorderId,
					$newOrderStatusId,
					false,
					null,
					'Automatically changed in accordance with the status in retailCRM'
				);
			}
		}
		catch (Exception|Error $e)
		{
			$message = (string) $e;
			Log::add($message, Log::ERROR, 'radicalmartretailcrm');
			throw $e;
		}

		return $this;
	}

	/**
	 * Publish a stock update request for products from one RetailCRM order.
	 *
	 * @return self
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function productsUpdateQuantityFromOrder(): self
	{
		$payload = [
			'retailcrm_order_id' => $this->getPositiveRouteId('retailcrm_order_id'),
		];
		$this->dispatchOperation('onWishboxRadicalMartRetailCrmUpdateQuantityFromOrder', $payload);

		return $this;
	}

	/**
	 * Publish a stock update request for RetailCRM offer external IDs.
	 *
	 * @return self
	 *
	 * @throws ApiExceptionInterface
	 * @throws ClientExceptionInterface
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function productsUpdateQuantity(): self
	{
		try
		{
			$data = $this->input->get(
				'data',
				json_decode($this->input->json->getRaw(), true),
				'array'
			);

			$retailCrmOfferExternalIds = $data['retailcrmoffer_externalids'];

			$app = $this->app;

			/** @var WishboxRadicalMartRetailCrmComponent $wishboxRadicalMartRetailCrmComponent */
			$wishboxRadicalMartRetailCrmComponent = $app->bootComponent('com_wishboxradicalmartretailcrm');

			$wishboxRadicalMartRetailCrmProductService =
				$wishboxRadicalMartRetailCrmComponent->getWishboxRadicalMartRetailCrmProductService();

			$wishboxRadicalMartRetailCrmProductService->updateQuantityByRetailCrmOfferExternalIds(
				1,
				100,
				$retailCrmOfferExternalIds
			);

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
	 * Publish a payment link creation request.
	 *
	 * @return self
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function ordersCreatePaymentLink(): self
	{
		$payload = [
			'retailcrm_order_id' => $this->getPositiveRouteId('retailcrm_order_id'),
		];
		$this->dispatchOperation('onWishboxRadicalMartRetailCrmCreatePaymentLink', $payload);

		return $this;
	}

	/**
	 * Publish a Russian Post shipping order creation request.
	 *
	 * @return self
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function ordersCreateRussianPostShippingOrder(): self
	{
		$payload = [
			'retailcrm_order_id' => $this->getPositiveRouteId('retailcrm_order_id'),
		];
		$this->dispatchOperation('onWishboxRadicalMartRetailCrmCreateRussianPostShippingOrder', $payload);

		return $this;
	}

	/**
	 * Decode the JSON request object.
	 *
	 * @return array<string, mixed>
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
	 * Validate an order tracking payload.
	 *
	 * @param   array<string, mixed>  $payload  Decoded request payload.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	private function validateTrackingPayload(array $payload): void
	{
		if ((int) ($payload['order_id'] ?? 0) <= 0)
		{
			throw new InvalidArgumentException('order_id must be greater than zero.', 400);
		}

		if (trim((string) ($payload['track_number'] ?? '')) === '')
		{
			throw new InvalidArgumentException('track_number is required.', 400);
		}
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
