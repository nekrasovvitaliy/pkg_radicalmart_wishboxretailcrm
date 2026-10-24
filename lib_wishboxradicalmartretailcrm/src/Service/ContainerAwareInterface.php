<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

use Psr\Container\ContainerInterface;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Defines a class that receives a service container.
 *
 * @since 1.0.0
 */
interface ContainerAwareInterface
{
	/**
	 * Set the service container.
	 *
	 * @param   ContainerInterface  $container  Service container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setContainer(ContainerInterface $container): void;
}
