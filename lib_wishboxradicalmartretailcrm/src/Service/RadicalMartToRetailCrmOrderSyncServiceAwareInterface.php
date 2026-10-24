<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Provides access to the RadicalMart-to-RetailCRM order synchronization service.
 *
 * @since 1.0.0
 */
interface RadicalMartToRetailCrmOrderSyncServiceAwareInterface
{
	/**
	 * Get the order synchronization service.
	 *
	 * @return RadicalMartToRetailCrmOrderSyncService Order synchronization service.
	 *
	 * @since 1.0.0
	 */
	public function getRadicalMartToRetailCrmOrderSyncService(): RadicalMartToRetailCrmOrderSyncService;

	/**
	 * Set the order synchronization service.
	 *
	 * @param   RadicalMartToRetailCrmOrderSyncService  $orderSyncService  Order synchronization service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setRadicalMartToRetailCrmOrderSyncService(
		RadicalMartToRetailCrmOrderSyncService $orderSyncService
	): void;
}
