<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

use UnexpectedValueException;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Provides order status synchronization service injection.
 *
 * @since 1.0.0
 */
trait RetailCrmToRadicalMartOrderStatusSyncServiceAwareTrait
{
	/**
	 * Order status synchronization service.
	 *
	 * @var RetailCrmToRadicalMartOrderStatusSyncService|null
	 *
	 * @since 1.0.0
	 */
	private ?RetailCrmToRadicalMartOrderStatusSyncService $orderStatusSyncService = null;

	/**
	 * Get the order status synchronization service.
	 *
	 * @return RetailCrmToRadicalMartOrderStatusSyncService
	 *
	 * @throws UnexpectedValueException When the service has not been injected.
	 *
	 * @since 1.0.0
	 */
	public function getRetailCrmToRadicalMartOrderStatusSyncService(): RetailCrmToRadicalMartOrderStatusSyncService
	{
		if ($this->orderStatusSyncService instanceof RetailCrmToRadicalMartOrderStatusSyncService)
		{
			return $this->orderStatusSyncService;
		}

		throw new UnexpectedValueException(
			'RetailCrmToRadicalMartOrderStatusSyncService not set in ' . static::class . '.'
		);
	}

	/**
	 * Set the order status synchronization service.
	 *
	 * @param   RetailCrmToRadicalMartOrderStatusSyncService  $orderStatusSyncService  Order status synchronisation service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setRetailCrmToRadicalMartOrderStatusSyncService(
		RetailCrmToRadicalMartOrderStatusSyncService $orderStatusSyncService
	): void
	{
		$this->orderStatusSyncService = $orderStatusSyncService;
	}
}
