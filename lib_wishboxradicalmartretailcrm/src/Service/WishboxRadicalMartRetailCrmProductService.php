<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

use Exception;
use RetailCrm\Api\Client;
use RetailCrm\Api\Enum\NumericBoolean;
use RetailCrm\Api\Exception\Api\AccountDoesNotExistException;
use RetailCrm\Api\Exception\Api\ApiErrorException;
use RetailCrm\Api\Exception\Api\MissingCredentialsException;
use RetailCrm\Api\Exception\Api\MissingParameterException;
use RetailCrm\Api\Exception\Api\ValidationException;
use RetailCrm\Api\Exception\Client\HandlerException;
use RetailCrm\Api\Exception\Client\HttpClientException;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Interfaces\ClientExceptionInterface;
use RetailCrm\Api\Model\Filter\Store\InventoryFilterType;
use RetailCrm\Api\Model\Request\Store\InventoriesRequest;
use RetailCrm\Api\Model\Response\Store\InventoriesResponse;
use WishboxRadicalMartLibrary\Service\Product\ProductService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Synchronises product inventory from RetailCRM.
 *
 * @since 1.0.0
 */
final class WishboxRadicalMartRetailCrmProductService
{
	/**
	 * Initialize the RadicalMart product service.
	 *
	 * @param   Client          $retailCrmClient            Shared RetailCRM API client.
	 * @param   ProductService  $radicalMartProductService  RadicalMart product service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private readonly Client         $retailCrmClient,
		private readonly ProductService $radicalMartProductService
	)
	{
	}

	/**
	 * @param   integer  $page                       Page
	 * @param   integer  $limit                      Limit
	 * @param   array    $retailCrmOfferExternalIds  Retail CRM offers external ids
	 *
	 * @return integer
	 *
	 * @throws AccountDoesNotExistException
	 * @throws ApiErrorException
	 * @throws ApiExceptionInterface
	 * @throws ClientExceptionInterface
	 * @throws HandlerException
	 * @throws HttpClientException
	 * @throws MissingCredentialsException
	 * @throws MissingParameterException
	 * @throws ValidationException
	 *
	 * @since 1.0.0
	 */
	public function updateQuantityByRetailCrmOfferExternalIds(
		int   $page = 1,
		int   $limit = 100,
		array $retailCrmOfferExternalIds = []
	): int
	{
		$response = $this->getQuantitiesByRetailCrmOfferExternalIds($page, $limit, $retailCrmOfferExternalIds);

		if (is_array($response->offers) && count($response->offers))
		{
			$this->updateQuantityByOffers($response->offers);
		}

		return $response->pagination->totalPageCount;
	}

	/**
	 * @param   integer  $page   Page
	 * @param   integer  $limit  Limit
	 * @param   array    $retailCrmOfferExternalIds
	 *
	 * @return InventoriesResponse
	 *
	 * @throws AccountDoesNotExistException
	 * @throws ApiErrorException
	 * @throws ApiExceptionInterface
	 * @throws ClientExceptionInterface
	 * @throws HandlerException
	 * @throws HttpClientException
	 * @throws MissingCredentialsException
	 * @throws MissingParameterException
	 * @throws ValidationException
	 *
	 * @since 1.0.0
	 */
	public function getQuantitiesByRetailCrmOfferExternalIds(
		int   $page = 1,
		int   $limit = 100,
		array $retailCrmOfferExternalIds = []
	): InventoriesResponse
	{
		$request                          = new InventoriesRequest;
		$request->filter                  = new InventoryFilterType;
		$request->filter->productActive   = NumericBoolean::TRUE;
		$request->filter->offerActive     = NumericBoolean::TRUE;
		$request->filter->offerExternalId = $retailCrmOfferExternalIds;
		$request->page                    = $page;
		$request->limit                   = $limit;

		return $this->retailCrmClient->store->inventories($request);
	}

	/**
	 * @param   array  $offers  Offers
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since 1.0.0
	 */
	public function updateQuantityByOffers(array $offers): bool
	{
		foreach ($offers as $offer)
		{
			$oldProductQuantity = $this->radicalMartProductService->getStockQuantity($offer->externalId);
			$newProductQuantity = (float) $offer->quantity;

			if ($oldProductQuantity != $newProductQuantity)
			{
				$this->radicalMartProductService->setStockQuantity(
					$offer->externalId,
					$newProductQuantity
				);
			}
		}

		return true;
	}
}
