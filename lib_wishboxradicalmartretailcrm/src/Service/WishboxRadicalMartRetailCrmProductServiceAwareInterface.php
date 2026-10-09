<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Defines the contract for classes that depend on the Wishbox RadicalMart product service.
 *
 * @since 1.0.0
 */
interface WishboxRadicalMartRetailCrmProductServiceAwareInterface
{
	/**
	 * Get the configured Wishbox RadicalMart product service.
	 *
	 * @return WishboxRadicalMartRetailCrmProductService
	 *
	 * @since 1.0.0
	 */
	public function getWishboxRadicalMartRetailCrmProductService(): WishboxRadicalMartRetailCrmProductService;

	/**
	 * Set the Wishbox RadicalMart product service.
	 *
	 * @param   WishboxRadicalMartRetailCrmProductService  $wishboxRadicalMartRetailCrmProductService  RadicalMart product service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setWishboxRadicalMartRetailCrmProductService(WishboxRadicalMartRetailCrmProductService $wishboxRadicalMartRetailCrmProductService): void;
}
