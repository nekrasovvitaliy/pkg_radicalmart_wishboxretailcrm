<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace Joomla\Component\WishboxRadicalMartRetailCrm\Administrator\Extension;

use Joomla\CMS\Extension\MVCComponent;
use WishboxRadicalMartLibrary\Service\Order\OrderServiceAwareInterface;
use WishboxRadicalMartLibrary\Service\Order\OrderServiceAwareTrait;
use WishboxRadicalMartRetailCrmLibrary\Service\RetailCrmToRadicalMartOrderStatusSyncServiceAwareInterface;
use WishboxRadicalMartRetailCrmLibrary\Service\RetailCrmToRadicalMartOrderStatusSyncServiceAwareTrait;
use WishboxRadicalMartRetailCrmLibrary\Service\WishboxRadicalMartRetailCrmProductServiceAwareInterface;
use WishboxRadicalMartRetailCrmLibrary\Service\WishboxRadicalMartRetailCrmProductServiceAwareTrait;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * WishBox RadicalMart RetailCRM component.
 *
 * @since 1.0.0
 */
final class WishboxRadicalMartRetailCrmComponent extends MVCComponent implements
	OrderServiceAwareInterface,
	RetailCrmToRadicalMartOrderStatusSyncServiceAwareInterface,
	WishboxRadicalMartRetailCrmProductServiceAwareInterface
{
	use OrderServiceAwareTrait;
	use RetailCrmToRadicalMartOrderStatusSyncServiceAwareTrait;
	use WishboxRadicalMartRetailCrmProductServiceAwareTrait;
}
