<?php
namespace ExchangeRates;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'exchange-rates' => [
                'type'    => 'Literal',
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/currencies',
                    'defaults' => [
                        'controller'    => Controller\CurrencyController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
					'currency' => [
						'type' => Segment::class,
						'options' => [
							'route' => '/:currencycode',
							'defaults' => [
								'action' => 'currency',
							],
							'constraints' => [
								'currencycode' => '[A-Z]{3}',
							],
						],
					],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'ExchangeRates' => __DIR__ . '/../view',
        ],
    ],
];
