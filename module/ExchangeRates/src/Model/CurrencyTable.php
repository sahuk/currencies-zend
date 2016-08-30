<?php
namespace ExchangeRates\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

class CurrencyTable
{
	private $tableGateway;

	public function __construct(TableGatewayInterface $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll()
	{
		return $this->tableGateway->select(function (Select $select) {
			$select->order('currency ASC');
		});
	}

	public function getCurrency($curcode)
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
