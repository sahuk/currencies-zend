<?php
namespace ExchangeRates\Model;

use RuntimeException;
use Zend\Db\Sql\Sql;

class RateHistory
{
	private $sql;

	public function __construct(Sql $sql)
	{
		$this->sql = $sql;
	}

	public function getHistory($curcode, $basecurrency)
	{
		$rowset = $this->tableGateway->select(['currency' => $curcode]);
		$row = $rowset->current();
		if (! $row) {
			throw new RuntimeException(sprintf(
				'Could not find currency with code %s',
				$curcode
			));
		}
		return $row;
	}
}
?>
