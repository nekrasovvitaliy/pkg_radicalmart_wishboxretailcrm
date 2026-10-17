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
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Interfaces\ClientExceptionInterface;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Handles RetailCRM product inventory callbacks.
 *
 * @since        1.0.0
 *
 * @noinspection PhpUnused
 */
final class ProductsController extends ApiController
{
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
	public function updateQuantity(): self
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
	 * Publish a stock update request for products from one RetailCRM order.
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
	public function updateQuantityFromOrder(): self
	{
		$retailCrmOrderId = $this->input->getInt('retailcrm_order_id');

		if ($retailCrmOrderId <= 0)
		{
			throw new InvalidArgumentException('retailcrm_order_id must be greater than zero.', 400);
		}

		try
		{
			$app = $this->app;

			/** @var WishboxRadicalMartRetailCrmComponent $wishboxRadicalMartRetailCrmComponent */
			$wishboxRadicalMartRetailCrmComponent = $app->bootComponent('com_wishboxradicalmartretailcrm');

			$wishboxRadicalMartRetailCrmProductService =
				$wishboxRadicalMartRetailCrmComponent->getWishboxRadicalMartRetailCrmProductService();

			$wishboxRadicalMartRetailCrmProductService->updateQuantityFromOrder($retailCrmOrderId);
		}
		catch (Exception|Error $e)
		{
			$message = (string) $e;
			Log::add($message, Log::ERROR, 'wishboxradicalmartretailcrm');
			throw $e;
		}

		return $this;
	}
}
