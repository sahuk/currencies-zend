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
	private $db;

	public function __construct(Sql $sql, $dbAdapter)
	{
		$this->sql = $sql;
		$this->db = $dbAdapter;
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
		if (count($result) == 0) {
			/*
			$select2 = $this->sql->select();
			$select2
				->from(['c' => 'currency_rates'])
				->join(
					['metric' => 'currency_rates'],
					'metric.day = c.day and c.basecurrency = metric.basecurrency',
					[
						'rate' => 'rate/c.rate',
						'currency' => 'currency',
						'day' => 'day',
						'timestamp' => 'timestamp'
					]
				)
				->order('day')
				->where([
					'c.currency' => $curcode,
					'metric.currency' => $metric
				]);
			$stmt2 = $this->sql->prepareStatementForSqlObject($select2);
			 */
			$sqlquery = "
				select
					metric.rate/c.rate as rate,
					metric.currency as currency,
					c.currency as basecurrency,
					c.day as day,
					c.timestamp as timestamp
				from
					currency_rates c inner join currency_rates metric
					on c.day = metric.day and c.basecurrency = metric.basecurrency
				where
					c.currency = ?
					and metric.currency = ?
				order by day ASC;
				";
			$stmt2 = $this->db->createStatement($sqlquery, [$curcode, $metric]);
			$result = $stmt2->execute();
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
