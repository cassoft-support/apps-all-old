<?php
//обработчик
function paySystemHandler($member, $key){
$paySystemHandler=[
'cs_modulbank_invoice' => [
                    'NAME' => 'CS-Modulbank-invoice',
                    'CODE' => 'cs_modulbank_invoice',
                    'SORT' => 100,
                    'SETTINGS' => [
                            'CURRENCY' => 'RUB',
                            'FORM_DATA' => [
                                    'ACTION_URI' =>  'https://city.brokci.ru/pub/cassoftApp/cloudReceiptsMB/tools/modulbankInvoice.php',
                                    'METHOD' => 'POST',
                                    'PARAMS' => [
                                            'merchant' => 'MERCHANT',
                                            'member' => 'MEMBER',
                                            'sum' => 'PAYMENT_SHOULD_PAY',
                                            'customer' => 'PAYMENT_BUYER_ID',
                                            'PAYMENT_ID' => 'PAYMENT_ID',
                                            'ORDER_ID' => 'ORDER_ID',
                                            'ACCOUNT_NUMBER' => 'ACCOUNT_NUMBER',
                                            'DATE_INSERT' => 'PAYMENT_DATE_INSERT',
                            ]
                                    ],
                            'CODES' => [
                                    'MERCHANT' => [
                                            'NAME' => 'Идентификатор магазина',
                                            'DESCRIPTION' => 'Идентификатор магазина клиента в системе Modulbank',
                                            'SORT' => 10,
                                            'DEFAULT' => [
                                                    'PROVIDER_KEY' => 'VALUE',
                                                    'PROVIDER_VALUE' => $key,
                                                ],
                                        ],
                                        'MEMBER' => [
                                          'NAME' => 'Идентификатор клиента',
                                          'DESCRIPTION' => 'Идентификатор клиента на портале',
                                          'SORT' => 100,
                                          'DEFAULT' => [
                                                  'PROVIDER_KEY' => 'VALUE',
                                                  'PROVIDER_VALUE' => $member,
                                              ],
                                      ],
                                    'PAYMENT_ID' => [
                                            'NAME' => 'Номер оплаты',
                                            'SORT' => 200,
                                            'GROUP' => 'PAYMENT',
                                            'DEFAULT' => [
                                                    'PROVIDER_KEY' => 'PAYMENT',
                                                    'PROVIDER_VALUE' => 'ID',
                                                ],
                                        ],
                                    'ORDER_ID' => [
                                            'NAME' => 'Код оплаты',
                                            'SORT' => 300,
                                            'GROUP' => 'ORDER',
                                            'DEFAULT' => [
                                                    'PROVIDER_KEY' => 'ORDER',
                                                    'PROVIDER_VALUE' => 'ID',
                                                ],
                                        ],
                                    'ACCOUNT_NUMBER' => [
                                            'NAME' => 'Номер платежа',
                                            'SORT' => 400,
                                            'GROUP' => 'PAYMENT',
                                            'DEFAULT' => [
                                                    'PROVIDER_KEY' => 'PAYMENT',
                                                    'PROVIDER_VALUE' => 'ACCOUNT_NUMBER',
                                                ],
                                        ],
                                    'PAYMENT_SHOULD_PAY' => [
                                            'NAME' => 'Сумма оплаты',
                                            'SORT' => 500,
                                            'GROUP' => 'PAYMENT',
                                            'DEFAULT' => [
                                                    'PROVIDER_KEY' => 'PAYMENT',
                                                    'PROVIDER_VALUE' => 'SUM',
                                                ],
                                        ],
                                    'PAYMENT_DATE_INSERT' => [
                                            'NAME' => 'Дата оплаты',
                                            'SORT' => 600,
                                            'GROUP' => 'PAYMENT',
                                            'DEFAULT' => [
                                                    'PROVIDER_KEY' => 'PAYMENT',
                                                    'PROVIDER_VALUE' => 'DATE_BILL_DATE',
                                                ],
                                        ],
                                    'PAYMENT_BUYER_ID' => [
                                            'NAME' => 'Код плательщика',
                                            'SORT' => 700,
                                            'DEFAULT' => [
                                                    'PROVIDER_KEY' => 'ORDER',
                                                    'PROVIDER_VALUE' => 'USER_ID',
                                                ],
                                        ],
                                ],
                        ],
                ],

            'cs_modulbank_order' => [                  
              'NAME' => 'CS-Modulbank-order',
              'CODE' => 'cs_modulbank_order',
              'SORT' => 100,
              'SETTINGS' => [
                      'CURRENCY' => 'RUB',
                      'FORM_DATA' => [
                              'ACTION_URI' =>  'https://city.brokci.ru/pub/cassoftApp/cloudReceiptsMB/tools/modulbankOrder.php',
                              'METHOD' => 'POST',
                              'PARAMS' => [
                                      'merchant' => 'MERCHANT',
                                      'member' => 'MEMBER',
                                      'sum' => 'PAYMENT_SHOULD_PAY',
                                      'customer' => 'PAYMENT_BUYER_ID',
                                      'PAYMENT_ID' => 'PAYMENT_ID',
                                      'ORDER_ID' => 'ORDER_ID',
                                      'ACCOUNT_NUMBER' => 'ACCOUNT_NUMBER',
                                      'DATE_INSERT' => 'PAYMENT_DATE_INSERT',
                      ]
                              ],
                      'CODES' => [
                              'MERCHANT' => [
                                      'NAME' => 'Идентификатор магазина',
                                      'DESCRIPTION' => 'Идентификатор магазина клиента в системе Modulbank',
                                      'SORT' => 10,
                                      'DEFAULT' => [
                                              'PROVIDER_KEY' => 'VALUE',
                                              'PROVIDER_VALUE' => $key,
                                          ],
                                  ],
                                  'MEMBER' => [
                                    'NAME' => 'Идентификатор клиента',
                                    'DESCRIPTION' => 'Идентификатор клиента на портале',
                                    'SORT' => 100,
                                    'DEFAULT' => [
                                            'PROVIDER_KEY' => 'VALUE',
                                            'PROVIDER_VALUE' => $member,
                                        ],
                                ],
                              'PAYMENT_ID' => [
                                      'NAME' => 'Номер оплаты',
                                      'SORT' => 200,
                                      'GROUP' => 'PAYMENT',
                                      'DEFAULT' => [
                                              'PROVIDER_KEY' => 'PAYMENT',
                                              'PROVIDER_VALUE' => 'ID',
                                          ],
                                  ],
                              'ORDER_ID' => [
                                      'NAME' => 'Код оплаты',
                                      'SORT' => 300,
                                      'GROUP' => 'ORDER',
                                      'DEFAULT' => [
                                              'PROVIDER_KEY' => 'ORDER',
                                              'PROVIDER_VALUE' => 'ID',
                                          ],
                                  ],
                              'ACCOUNT_NUMBER' => [
                                      'NAME' => 'Номер платежа',
                                      'SORT' => 400,
                                      'GROUP' => 'PAYMENT',
                                      'DEFAULT' => [
                                              'PROVIDER_KEY' => 'PAYMENT',
                                              'PROVIDER_VALUE' => 'ACCOUNT_NUMBER',
                                          ],
                                  ],
                              'PAYMENT_SHOULD_PAY' => [
                                      'NAME' => 'Сумма оплаты',
                                      'SORT' => 500,
                                      'GROUP' => 'PAYMENT',
                                      'DEFAULT' => [
                                              'PROVIDER_KEY' => 'PAYMENT',
                                              'PROVIDER_VALUE' => 'SUM',
                                          ],
                                  ],
                              'PAYMENT_DATE_INSERT' => [
                                      'NAME' => 'Дата оплаты',
                                      'SORT' => 600,
                                      'GROUP' => 'PAYMENT',
                                      'DEFAULT' => [
                                              'PROVIDER_KEY' => 'PAYMENT',
                                              'PROVIDER_VALUE' => 'DATE_BILL_DATE',
                                          ],
                                  ],
                              'PAYMENT_BUYER_ID' => [
                                      'NAME' => 'Код плательщика',
                                      'SORT' => 700,
                                      'DEFAULT' => [
                                              'PROVIDER_KEY' => 'ORDER',
                                              'PROVIDER_VALUE' => 'USER_ID',
                                          ],
                                  ],
                          ],
                  ],

                ],
              ];
              return $paySystemHandler;
            }