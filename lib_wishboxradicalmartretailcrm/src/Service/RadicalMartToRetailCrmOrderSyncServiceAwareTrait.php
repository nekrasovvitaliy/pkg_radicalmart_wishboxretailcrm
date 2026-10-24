<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UnexpectedValueException;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Implements access to the RadicalMart-to-RetailCRM order synchronization service.
 *
 * @since 1.0.0
 */
trait RadicalMartToRetailCrmOrderSyncServiceAwareTrait
{
	use ContainerAwareTrait;

	/**
	 * Order synchronization service.
	 *
	 * @var RadicalMartToRetailCrmOrderSyncService|null
	 *
	 * @since 1.0.0
	 */
	private ?RadicalMartToRetailCrmOrderSyncService $radicalMartToRetailCrmOrderSyncService = null;

	/**
	 * Get the order synchronization service.
	 *
	 * @return RadicalMartToRetailCrmOrderSyncService Order synchronization service.
	 *
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 *
	 * @since 1.0.0
	 */
	public function getRadicalMartToRetailCrmOrderSyncService(): RadicalMartToRetailCrmOrderSyncService
	{
		if ($this->radicalMartToRetailCrmOrderSyncService instanceof RadicalMartToRetailCrmOrderSyncService)
		{
			return $this->radicalMartToRetailCrmOrderSyncService;
		}

		$orderSyncService = $this->getContainer()->get(RadicalMartToRetailCrmOrderSyncService::class);

		if (!$orderSyncService instanceof RadicalMartToRetailCrmOrderSyncService)
		{
			throw new UnexpectedValueException(
				'Container did not return RadicalMartToRetailCrmOrderSyncService in ' . static::class . '.'
			);
		}

		$this->radicalMartToRetailCrmOrderSyncService = $orderSyncService;

		return $this->radicalMartToRetailCrmOrderSyncService;
	}

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
	): void
	{
		$this->radicalMartToRetailCrmOrderSyncService = $orderSyncService;
	}
}
