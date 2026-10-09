<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

use UnexpectedValueException;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Provides Wishbox RadicalMart service injection for service-aware classes.
 *
 * @since 1.0.0
 */
trait WishboxRadicalMartRetailCrmProductServiceAwareTrait
{
	/**
	 * Wishbox RadicalMart product service.
	 *
	 * @var WishboxRadicalMartRetailCrmProductService|null
	 *
	 * @since 1.0.0
	 */
	private ?WishboxRadicalMartRetailCrmProductService $wishboxRadicalMartRetailCrmProductService = null;

	/**
	 * Get the configured Wishbox RadicalMart product service.
	 *
	 * @return WishboxRadicalMartRetailCrmProductService
	 *
	 * @throws UnexpectedValueException When the service has not been injected.
	 *
	 * @since 1.0.0
	 */
	public function getWishboxRadicalMartRetailCrmProductService(): WishboxRadicalMartRetailCrmProductService
	{
		if ($this->wishboxRadicalMartRetailCrmProductService instanceof WishboxRadicalMartRetailCrmProductService)
		{
			return $this->wishboxRadicalMartRetailCrmProductService;
		}

		throw new UnexpectedValueException('WishboxRadicalMartRetailCrmProductService not set in ' . static::class . '.');
	}

	/**
	 * Set the Wishbox RadicalMart product service.
	 *
	 * @param   WishboxRadicalMartRetailCrmProductService  $wishboxRadicalMartRetailCrmProductService  RadicalMart product service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setWishboxRadicalMartRetailCrmProductService(WishboxRadicalMartRetailCrmProductService $wishboxRadicalMartRetailCrmProductService): void
	{
		$this->wishboxRadicalMartRetailCrmProductService = $wishboxRadicalMartRetailCrmProductService;
	}
}
