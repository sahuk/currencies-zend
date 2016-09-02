<?php
/**
* @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
* @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace ExchangeRates\Controller;

use ExchangeRates\Model\CurrencyTable;
use ExchangeRates\Model\RateHistory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Sql;

class CurrencyController extends AbstractActionController
{

	private $table;
	private $rh;

	public function __construct(CurrencyTable $table, RateHistory $rh)
	{
		$this->table = $table;
		$this->rh = $rh;
	}

    public function indexAction()
    {
		return new ViewModel([
			'currencies' => $this->table->fetchAll(),
		]);
    }

	public function currencyAction()
	{
		$currencycode = $this->params()->fromRoute('currencycode');
		$currency = $this->table->getCurrency($currencycode);
		$metric = 'EUR';
		$history = $this->rh->getHistory($currencycode, $metric);
		$chartdata = array();
		foreach ($history as $entry) {
			array_push($chartdata, [
				'date' => $entry->date->format('Y-m-d'),
					'value' => $entry->rate
				]);
		}
		return new ViewModel([
			'currencycode' => $currencycode,
			'name' => $currency->name,
			'rates' => $history,
			'latest' => end($history),
			'metric' => $metric,
			'jsondata' => json_encode([
				'chart' => $chartdata,
				'metric' => $metric
				])
		]);
	}
}
