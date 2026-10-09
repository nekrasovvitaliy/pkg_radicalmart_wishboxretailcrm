<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers the web services plugin installer.
 *
 * @since 1.0.0
 */
return new class implements ServiceProviderInterface
{
	/**
	 * Register the plugin installer.
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
			 * Enables the plugin after installation.
			 *
			 * @since 1.0.0
			 */
			new readonly class ($container->get(DatabaseDriver::class)) implements InstallerScriptInterface
			{
				/**
				 * Create the plugin installer.
				 *
				 * @param   DatabaseDriver  $db  Joomla database driver.
				 *
				 * @return void
				 *
				 * @since 1.0.0
				 */
				public function __construct(private DatabaseDriver $db)
				{
				}

				/**
				 * Enable the plugin after installation.
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
					$this->enablePlugin($adapter);

					return true;
				}

				/**
				 * Handle plugin update.
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
				 * Handle plugin removal.
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
				 * Prepare an installer operation.
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

				/**
				 * Enable the installed plugin record.
				 *
				 * @param   InstallerAdapter  $adapter  Installer adapter.
				 *
				 * @return void
				 *
				 * @since 1.0.0
				 */
				private function enablePlugin(InstallerAdapter $adapter): void
				{
					$plugin = new stdClass();
					$plugin->type = 'plugin';
					$plugin->element = $adapter->getElement();
					$plugin->folder = (string) $adapter->getParent()->manifest->attributes()['group'];
					$plugin->enabled = 1;

					$this->db->updateObject('#__extensions', $plugin, ['type', 'element', 'folder']);
				}
			}
		);
	}
};
