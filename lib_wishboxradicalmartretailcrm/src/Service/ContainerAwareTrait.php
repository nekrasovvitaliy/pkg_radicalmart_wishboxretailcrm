<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Service;

use Psr\Container\ContainerInterface;
use UnexpectedValueException;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Provides access to a service container.
 *
 * @since 1.0.0
 */
trait ContainerAwareTrait
{
	/**
	 * Service container.
	 *
	 * @var ContainerInterface|null
	 *
	 * @since 1.0.0
	 */
	private ?ContainerInterface $container = null;

	/**
	 * Get the service container.
	 *
	 * @return ContainerInterface Service container.
	 *
	 * @throws UnexpectedValueException When the container has not been set.
	 *
	 * @since 1.0.0
	 */
	protected function getContainer(): ContainerInterface
	{
		if ($this->container instanceof ContainerInterface)
		{
			return $this->container;
		}

		throw new UnexpectedValueException('Container not set in ' . static::class . '.');
	}

	/**
	 * Set the service container.
	 *
	 * @param   ContainerInterface  $container  Service container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setContainer(ContainerInterface $container): void
	{
		$this->container = $container;
	}
}
