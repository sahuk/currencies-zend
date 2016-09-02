<?php
namespace ExchangeRates\Model;

use RuntimeException;
use DateTime;
use DateInterval;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Hydrator\ArraySerializable;


class Rate
{
	public $rate;
	public $date;
	public $metric;
	public $scrape_time;

	static function parseDate($ordinal)
	{
		$epoch = DateTime::createFromFormat('Y-m-d', '0001-01-01');
		$diff = new DateInterval('P' . $ordinal . 'D');
		return $epoch->add($diff);
	}

	static function parseTimestamp($unixtime)
	{
		return DateTime::createFromFormat('U', $unixtime);
	}

	public function exchangeArray(array $data)
	{
		$this->rate = !empty($data['rate']) ? $data['rate'] : null;
		$this->date = !empty($data['day']) ?
			self::parseDate($data['day']) : new DateTime();
		$this->scrape_time = !empty($data['timestamp']) ?
			self::parseTimestamp($data['timestamp']) : new DateTime();
		$this->metric = !empty($data['currency']) ? !empty($data['currency']) : null;
	}
}

class RateHistory
{
	private $sql;

	public function __construct(Sql $sql)
	{
		$this->sql = $sql;
	}

	public function getHistory($curcode, $metric)
	{
		$select = $this->sql->select();
		$select->from('currency_rates')->where([
			'basecurrency' => $curcode,
			'currency' => $metric
		])->order('day');
		$stmt = $this->sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		if ($result->getFieldCount() == 0) {
			throw new RuntimeException(
				sprintf('Could not find currency with code %s', $curcode));
		}
		if ($result instanceof ResultInterface && $result->isQueryResult()) {
			$resultSet = new HydratingResultSet(
				new ArraySerializable,
				new Rate
			);
			$resultSet->initialize($result);
			$resultSet->buffer();
			return $resultSet;
		}
	}
}
?>
