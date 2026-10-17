<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Defines access to the retailCRM-to-RadicalMart order status synchronisation service.
 *
 * @since 1.0.0
 */
interface RetailCrmToRadicalMartOrderStatusSyncServiceAwareInterface
{
	/**
	 * Get the order status synchronisation service.
	 *
	 * @return RetailCrmToRadicalMartOrderStatusSyncService
	 *
	 * @since 1.0.0
	 */
	public function getRetailCrmToRadicalMartOrderStatusSyncService(): RetailCrmToRadicalMartOrderStatusSyncService;

	/**
	 * Set the order status synchronisation service.
	 *
	 * @param   RetailCrmToRadicalMartOrderStatusSyncService  $orderStatusSyncService  Order status synchronisation service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setRetailCrmToRadicalMartOrderStatusSyncService(
		RetailCrmToRadicalMartOrderStatusSyncService $orderStatusSyncService
	): void;
}
