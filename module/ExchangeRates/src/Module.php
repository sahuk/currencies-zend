<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ExchangeRates;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

	public function getServiceConfig()
	{
		return [
			'factories' => [
				Model\CurrencyTable::class => function($container) {
					$tableGateway =  $container->get(Model\CurrencyTableGateway::class);
					return new Model\CurrencyTable($tableGateway);
				},
				Model\CurrencyTableGateway::class => function($container) {
					$dbAdapter = $container->get(AdapterInterface::class);
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Currency());
					return new TableGateway(
						'currency_codes', $dbAdapter, null, $resultSetPrototype);
				},
			],
		];
	}

	public function getControllerConfig()
	{
		return [
			'factories' => [
				Controller\CurrencyController::class => function ($container) {
					return new Controller\CurrencyController(
						$container->get(Model\CurrencyTable::class)

					);
				}
			]
		];
	}
}
