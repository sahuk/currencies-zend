<?php
/**
* @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
* @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace ExchangeRates\Controller;

use ExchangeRates\Model\CurrencyTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Sql;

class CurrencyController extends AbstractActionController
{

	private $table;
	private $sql;

	public function __construct(CurrencyTable $table, Sql $sql)
	{
		$this->table = $table;
		$this->sql = $sql;
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
		return new ViewModel([
			'currencycode' => $currencycode,
			'name' => $currency->name,
		]);
	}
}
