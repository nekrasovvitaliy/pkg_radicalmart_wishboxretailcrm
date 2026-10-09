<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers the package installer script.
 *
 * @since 1.0.0
 */
return new class implements ServiceProviderInterface
{
	/**
	 * Register the package installer.
	 *
	 * @param   Container  $container  Dependency injection container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function register(Container $container): void
	{
		$container->set(
			InstallerScriptInterface::class,
			/**
			 * Validates the Joomla runtime before installation.
			 *
			 * @since 1.0.0
			 */
			new class ($container->get(AdministratorApplication::class)) implements InstallerScriptInterface
			{
				private const MINIMUM_JOOMLA = '6.1.4';

				private const MINIMUM_PHP = '8.5';

				/**
				 * Create the package installer.
				 *
				 * @param   AdministratorApplication  $app  Administrator application.
				 *
				 * @return void
				 *
				 * @since 1.0.0
				 */
				public function __construct(private AdministratorApplication $app)
				{
				}

				/**
				 * Handle package installation.
				 *
				 * @param   InstallerAdapter  $adapter  Installer adapter.
				 *
				 * @return boolean
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function install(InstallerAdapter $adapter): bool
				{
					return true;
				}

				/**
				 * Handle package update.
				 *
				 * @param   InstallerAdapter  $adapter  Installer adapter.
				 *
				 * @return boolean
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function update(InstallerAdapter $adapter): bool
				{
					return true;
				}

				/**
				 * Handle package removal.
				 *
				 * @param   InstallerAdapter  $adapter  Installer adapter.
				 *
				 * @return boolean
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function uninstall(InstallerAdapter $adapter): bool
				{
					return true;
				}

				/**
				 * Validate runtime compatibility before installation.
				 *
				 * @param   string            $type     Installer operation type.
				 * @param   InstallerAdapter  $adapter  Installer adapter.
				 *
				 * @return boolean
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function preflight(string $type, InstallerAdapter $adapter): bool
				{
					if ($type === 'uninstall')
					{
						return true;
					}

					$version = new Version();

					if (!$version->isCompatible(self::MINIMUM_JOOMLA))
					{
						$this->app->enqueueMessage(
							Text::sprintf('PKG_RADICALMART_WISHBOXRETAILCRM_ERROR_JOOMLA', self::MINIMUM_JOOMLA),
							'error'
						);

						return false;
					}

					if (version_compare(PHP_VERSION, self::MINIMUM_PHP, '<'))
					{
						$this->app->enqueueMessage(
							Text::sprintf('PKG_RADICALMART_WISHBOXRETAILCRM_ERROR_PHP', self::MINIMUM_PHP),
							'error'
						);

						return false;
					}

					return true;
				}

				/**
				 * Complete an installer operation.
				 *
				 * @param   string            $type     Installer operation type.
				 * @param   InstallerAdapter  $adapter  Installer adapter.
				 *
				 * @return boolean
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function postflight(string $type, InstallerAdapter $adapter): bool
				{
					return true;
				}
			}
		);
	}
};
