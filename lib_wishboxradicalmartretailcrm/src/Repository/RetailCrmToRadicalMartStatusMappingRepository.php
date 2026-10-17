<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartRetailCrmLibrary\Repository;

use InvalidArgumentException;
use Joomla\Database\DatabaseInterface;
use RuntimeException;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Resolves RadicalMart status mappings configured for retailCRM.
 *
 * @since 1.0.0
 */
final class RetailCrmToRadicalMartStatusMappingRepository
{
	/**
	 * Initialise the status mapping repository.
	 *
	 * @param   DatabaseInterface  $database  Joomla database connection.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function __construct(private readonly DatabaseInterface $database)
	{
	}

	/**
	 * Find the RadicalMart status mapped to a retailCRM status code.
	 *
	 * @param   string  $retailCrmOrderStatusCode  retailCRM order status code.
	 *
	 * @return int RadicalMart status identifier.
	 *
	 * @throws InvalidArgumentException When the retailCRM status code is empty.
	 * @throws RuntimeException When no RadicalMart status is mapped.
	 *
	 * @since 1.0.0
	 */
	public function getRadicalMartStatusId(string $retailCrmOrderStatusCode): int
	{
		$retailCrmOrderStatusCode = trim($retailCrmOrderStatusCode);

		if ($retailCrmOrderStatusCode === '')
		{
			throw new InvalidArgumentException('retailcrmorderstatuscode is required.', 400);
		}

		$query = $this->database->createQuery()
			->select($this->database->quoteName('id'))
			->from($this->database->quoteName('#__radicalmart_statuses'))
			->where(
				'JSON_UNQUOTE(JSON_EXTRACT('
				. $this->database->quoteName('plugins')
				. ', '
				. $this->database->quote('$.retailcrm_status_code')
				. ')) = :retailCrmOrderStatusCode'
			)
			->bind(':retailCrmOrderStatusCode', $retailCrmOrderStatusCode);

		$statusId = (int) $this->database->setQuery($query, 0, 1)->loadResult();

		if ($statusId < 1)
		{
			throw new RuntimeException(
				'RadicalMart status mapped to retailCRM code "' . $retailCrmOrderStatusCode . '" was not found.',
				404
			);
		}

		return $statusId;
	}
}
