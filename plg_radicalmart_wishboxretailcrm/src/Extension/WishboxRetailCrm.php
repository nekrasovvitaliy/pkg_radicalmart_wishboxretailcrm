<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later
 */

namespace Joomla\Plugin\RadicalMart\WishboxRetailCrm\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use stdClass;
use Throwable;
use WishboxRadicalMartRetailCrmLibrary\Service\ContainerAwareInterface;
use WishboxRadicalMartRetailCrmLibrary\Service\RadicalMartToRetailCrmOrderSyncServiceAwareInterface;
use WishboxRadicalMartRetailCrmLibrary\Service\RadicalMartToRetailCrmOrderSyncServiceAwareTrait;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Synchronizes saved RadicalMart orders with RetailCRM.
 *
 * @since 1.0.0
 */
final class WishboxRetailCrm extends CMSPlugin implements
	ContainerAwareInterface,
	RadicalMartToRetailCrmOrderSyncServiceAwareInterface,
	SubscriberInterface
{
	use RadicalMartToRetailCrmOrderSyncServiceAwareTrait;

	/**
	 * Autoload the language file.
	 *
	 * @var boolean
	 *
	 * @since 1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Return the subscribed RadicalMart events.
	 *
	 * @return array<string, string> Event handlers indexed by event name.
	 *
	 * @since 1.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onRadicalMartAfterOrderSave' => 'onRadicalMartAfterOrderSave',
		];
	}

	/**
	 * Synchronize a saved RadicalMart order with RetailCRM.
	 *
	 * @param   string    $context   RadicalMart model context.
	 * @param   array     $formData  Original form data.
	 * @param   array     $data      Processed order data.
	 * @param   stdClass  $order     Saved RadicalMart order.
	 * @param   boolean   $isNew     Whether the order has just been created.
	 *
	 * @return void
	 *
	 * @throws Throwable When the order cannot be synchronized with RetailCRM.
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function onRadicalMartAfterOrderSave(
		string $context,
		array $formData,
		array $data,
		stdClass $order,
		bool $isNew
	): void
	{
		$this->getRadicalMartToRetailCrmOrderSyncService()
			->sync($order);
	}
}
