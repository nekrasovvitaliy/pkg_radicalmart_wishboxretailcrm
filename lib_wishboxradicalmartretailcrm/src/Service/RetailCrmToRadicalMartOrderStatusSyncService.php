<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

use Exception;
use WishboxRadicalMartLibrary\Service\Order\OrderService;
use WishboxRadicalMartRetailCrmLibrary\Repository\RetailCrmToRadicalMartStatusMappingRepository;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Synchronizes a retailCRM order status with RadicalMart.
 *
 * @since 1.0.0
 */
final readonly class RetailCrmToRadicalMartOrderStatusSyncService
{
	/**
	 * Initialize the order status synchronization service.
	 *
	 * @param   RetailCrmToRadicalMartStatusMappingRepository  $statusMappingRepository  Status mapping repository.
	 * @param   OrderService                                   $radicalMartOrderService  RadicalMart order service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private RetailCrmToRadicalMartStatusMappingRepository $statusMappingRepository,
		private OrderService                                  $radicalMartOrderService
	)
	{
	}

	/**
	 * Update a RadicalMart order from a retailCRM status code.
	 *
	 * @param   int     $radicalMartOrderId        RadicalMart order identifier.
	 * @param   string  $retailCrmOrderStatusCode  retailCRM order status code.
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 1.0.0
	 */
	public function updateStatus(int $radicalMartOrderId, string $retailCrmOrderStatusCode): void
	{
		$radicalMartStatusId = $this->statusMappingRepository
			->getRadicalMartStatusId($retailCrmOrderStatusCode);

		$this->radicalMartOrderService->updateStatus($radicalMartOrderId, $radicalMartStatusId);
	}
}
