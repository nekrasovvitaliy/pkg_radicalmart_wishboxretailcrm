<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

use DateMalformedStringException;
use DateTime;
use InvalidArgumentException;
use Joomla\Registry\Registry;
use RetailCrm\Api\Client;
use RetailCrm\Api\Enum\ByIdentifier;
use RetailCrm\Api\Exception\Api\AccountDoesNotExistException;
use RetailCrm\Api\Exception\Api\ApiErrorException;
use RetailCrm\Api\Exception\Api\MissingCredentialsException;
use RetailCrm\Api\Exception\Api\MissingParameterException;
use RetailCrm\Api\Exception\Api\ValidationException;
use RetailCrm\Api\Exception\Client\HandlerException;
use RetailCrm\Api\Exception\Client\HttpClientException;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Interfaces\ClientExceptionInterface;
use RetailCrm\Api\Model\Entity\Orders\Delivery\OrderDeliveryAddress;
use RetailCrm\Api\Model\Entity\Orders\Delivery\SerializedOrderDelivery;
use RetailCrm\Api\Model\Entity\Orders\Items\Offer;
use RetailCrm\Api\Model\Entity\Orders\Items\OrderProduct;
use RetailCrm\Api\Model\Entity\Orders\Order;
use RetailCrm\Api\Model\Filter\Orders\OrderFilter;
use RetailCrm\Api\Model\Request\Orders\OrdersCreateRequest;
use RetailCrm\Api\Model\Request\Orders\OrdersEditRequest;
use RetailCrm\Api\Model\Request\Orders\OrdersRequest;
use RuntimeException;
use stdClass;
use Throwable;
use WishboxRetailCrmLibrary\Configuration\RetailCrmConfiguration;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Creates or updates RetailCRM orders from RadicalMart orders.
 *
 * @since 1.0.0
 */
final readonly class RadicalMartToRetailCrmOrderSyncService
{
	/**
	 * Initialize the order synchronization service.
	 *
	 * @param   Client                  $retailCrmClient         RetailCRM API client.
	 * @param   RetailCrmConfiguration  $retailCrmConfiguration  RetailCRM connection settings.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private Client                 $retailCrmClient,
		private RetailCrmConfiguration $retailCrmConfiguration
	)
	{
	}

	/**
	 * Create or update an order in RetailCRM.
	 *
	 * @param   stdClass  $radicalMartOrder  Saved RadicalMart order.
	 *
	 * @return void
	 *
	 * @throws InvalidArgumentException When the RadicalMart order is invalid.
	 * @throws RuntimeException When RetailCRM configuration or response is invalid.
	 * @throws Throwable When a RetailCRM API request fails.
	 *
	 * @since 1.0.0
	 */
	public function sync(stdClass $radicalMartOrder): void
	{
		$orderId = (int) ($radicalMartOrder->id ?? 0);

		if ($orderId < 1)
		{
			throw new InvalidArgumentException('RadicalMart order ID must be more than zero.');
		}

		$site = trim($this->retailCrmConfiguration->site);

		if ($site === '')
		{
			throw new RuntimeException('RetailCRM site code must be configured.');
		}

		$externalId     = (string) $orderId;
		$retailCrmOrder = $this->buildOrder($radicalMartOrder, $externalId, $site);

		if ($this->orderExists($externalId, $site))
		{
			$request        = new OrdersEditRequest();
			$request->by    = ByIdentifier::EXTERNAL_ID;
			$request->site  = $site;
			$request->order = $retailCrmOrder;
			$response       = $this->retailCrmClient->orders->edit($externalId, $request);
		}
		else
		{
			$request        = new OrdersCreateRequest();
			$request->site  = $site;
			$request->order = $retailCrmOrder;
			$response       = $this->retailCrmClient->orders->create($request);
		}

		if ($response->success !== true)
		{
			throw new RuntimeException('RetailCRM did not synchronize RadicalMart order ' . $orderId . '.');
		}
	}

	/**
	 * Build a RetailCRM order from a RadicalMart order.
	 *
	 * @param   stdClass  $radicalMartOrder  RadicalMart order.
	 * @param   string    $externalId        RetailCRM external order identifier.
	 * @param   string    $site              RetailCRM site code.
	 *
	 * @return Order RetailCRM order.
	 *
	 * @throws DateMalformedStringException
	 *
	 * @since 1.0.0
	 */
	private function buildOrder(stdClass $radicalMartOrder, string $externalId, string $site): Order
	{
		$formData               = is_array($radicalMartOrder->formData ?? null) ? $radicalMartOrder->formData : [];
		$contacts               = is_array($formData['contacts'] ?? null) ? $formData['contacts'] : [];
		$order                  = new Order();
		$order->externalId      = $externalId;
		$order->number          = trim((string) ($radicalMartOrder->number ?? ''));
		$order->site            = $site;
		$order->currency        = trim((string) ($radicalMartOrder->currency['code'] ?? $formData['currency'] ?? 'RUB'));
		$order->orderType       = 'eshop-individual';
		$order->orderMethod     = 'shopping-cart';
		$order->firstName       = trim((string) ($contacts['first_name'] ?? ''));
		$order->lastName        = trim((string) ($contacts['last_name'] ?? ''));
		$order->patronymic      = trim((string) ($contacts['second_name'] ?? ''));
		$order->phone           = trim((string) ($contacts['phone'] ?? ''));
		$order->email           = trim((string) ($contacts['email'] ?? ''));
		$order->customerComment = trim((string) ($radicalMartOrder->note ?? $formData['note'] ?? ''));
		$order->items           = $this->buildItems($radicalMartOrder);
		$order->delivery        = $this->buildDelivery($radicalMartOrder, $formData);

		$created = trim((string) ($radicalMartOrder->created ?? $formData['created'] ?? ''));

		if ($created !== '')
		{
			$order->createdAt = new DateTime($created);
		}

		$statusCode = $this->getStatusCode($radicalMartOrder);

		if ($statusCode !== '')
		{
			$order->status = $statusCode;
		}

		return $order;
	}

	/**
	 * Build RetailCRM order products.
	 *
	 * @param   stdClass  $radicalMartOrder  RadicalMart order.
	 *
	 * @return list<OrderProduct> RetailCRM order products.
	 *
	 * @throws InvalidArgumentException When the order has no valid products.
	 *
	 * @since 1.0.0
	 */
	private function buildItems(stdClass $radicalMartOrder): array
	{
		$products = is_array($radicalMartOrder->products ?? null) ? $radicalMartOrder->products : [];
		$items    = [];

		foreach ($products as $product)
		{
			if (!is_object($product) || (int) ($product->id ?? 0) < 1)
			{
				continue;
			}

			$orderData          = is_array($product->order ?? null) ? $product->order : [];
			$offer              = new Offer();
			$offer->externalId  = (string) $product->id;
			$item               = new OrderProduct();
			$item->offer        = $offer;
			$item->quantity     = max(1.0, (float) ($orderData['quantity'] ?? 1));
			$item->initialPrice = (float) ($orderData['final'] ?? $orderData['base'] ?? 0);
			$item->productName  = trim((string) ($product->title ?? $product->name ?? ''));
			$items[]            = $item;
		}

		if ($items === [])
		{
			throw new InvalidArgumentException('RadicalMart order must contain at least one product.');
		}

		return $items;
	}

	/**
	 * Build RetailCRM delivery data.
	 *
	 * @param   stdClass              $radicalMartOrder  RadicalMart order.
	 * @param   array<string, mixed>  $formData          RadicalMart order form data.
	 *
	 * @return SerializedOrderDelivery|null RetailCRM delivery data.
	 *
	 * @since 1.0.0
	 */
	private function buildDelivery(stdClass $radicalMartOrder, array $formData): ?SerializedOrderDelivery
	{
		$shippingData = is_array($formData['shipping'] ?? null) ? $formData['shipping'] : [];
		$shipping     = is_object($radicalMartOrder->shipping ?? null) ? $radicalMartOrder->shipping : null;

		if ($shipping === null && $shippingData === [])
		{
			return null;
		}

		$shippingOrder     = is_object($shipping->order ?? null) ? $shipping->order : null;
		$price             = is_array($shippingOrder->price ?? null) ? $shippingOrder->price : [];
		$shippingPrice     = is_array($shippingData['price'] ?? null) ? $shippingData['price'] : [];
		$delivery          = new SerializedOrderDelivery();
		$delivery->code    = trim($shippingOrder->code ?? $shipping->code ?? '');
		$delivery->cost    = (float) ($price['final'] ?? $price['base'] ?? $shippingPrice['final']
			?? $shippingPrice['base'] ?? 0);
		$address           = new OrderDeliveryAddress();
		$address->index    = trim((string) ($shippingData['zip'] ?? ''));
		$address->region   = trim((string) ($shippingData['region'] ?? ''));
		$address->city     = trim((string) ($shippingData['city'] ?? ''));
		$address->street   = trim((string) ($shippingData['street'] ?? ''));
		$address->house    = trim((string) ($shippingData['house'] ?? ''));
		$address->housing  = trim((string) ($shippingData['building'] ?? ''));
		$address->flat     = trim((string) ($shippingData['apartment'] ?? ''));
		$address->text     = trim((string) ($shippingData['address_string'] ?? $shippingOrder->address_string ?? ''));
		$delivery->address = $address;

		return $delivery;
	}

	/**
	 * Get the RetailCRM status code configured for a RadicalMart status.
	 *
	 * @param   stdClass  $radicalMartOrder  RadicalMart order.
	 *
	 * @return string RetailCRM status code or an empty string.
	 *
	 * @since 1.0.0
	 */
	private function getStatusCode(stdClass $radicalMartOrder): string
	{
		$plugins = $radicalMartOrder->status->plugins ?? null;

		if ($plugins instanceof Registry)
		{
			return trim((string) $plugins->get('retailcrm_status_code', ''));
		}

		if (is_array($plugins))
		{
			return trim((string) ($plugins['retailcrm_status_code'] ?? ''));
		}

		return '';
	}

	/**
	 * Check whether the RetailCRM order already exists.
	 *
	 * @param   string  $externalId  External order identifier.
	 * @param   string  $site        RetailCRM site code.
	 *
	 * @return boolean Whether the order exists.
	 *
	 * @throws AccountDoesNotExistException
	 * @throws ApiErrorException
	 * @throws MissingCredentialsException
	 * @throws MissingParameterException
	 * @throws ValidationException
	 * @throws HandlerException
	 * @throws HttpClientException
	 * @throws ApiExceptionInterface
	 * @throws ClientExceptionInterface
	 *
	 * @since 1.0.0
	 */
	private function orderExists(string $externalId, string $site): bool
	{
		$request                      = new OrdersRequest();
		$request->limit               = 20;
		$request->page                = 1;
		$request->filter              = new OrderFilter();
		$request->filter->externalIds = [$externalId];
		$request->filter->sites       = [$site];
		$response                     = $this->retailCrmClient->orders->list($request);

		return is_array($response->orders ?? null) && $response->orders !== [];
	}
}
