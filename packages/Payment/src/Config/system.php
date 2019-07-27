<?php

return [
    [
        'key' => 'sales.paymentmethods.altynasyr',
        'name' => 'admin::app.admin.system.altyn-asyr',
        'sort' => 4,
        'fields' => [
            [
                'name' => 'title',
                'title' => 'admin::app.admin.system.title',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'description',
                'title' => 'admin::app.admin.system.description',
                'type' => 'textarea',
                'channel_based' => false,
                'locale_based' => true
            ],  [
                'name' => 'business_account',
                'title' => 'admin::app.admin.system.business-account',
//                'type' => 'select',
                'type' => 'text',
                'validation' => 'required'
            ],[
                'name' => 'account_password',
                'title' => 'admin::app.account.password',
//                'type' => 'select',
                'type' => 'password',
                'validation' => 'required'
            ],  [
                'name' => 'active',
                'title' => 'admin::app.admin.system.status',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Active',
                        'value' => true
                    ], [
                        'title' => 'Inactive',
                        'value' => false
                    ]
                ],
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ],  [
                'name' => 'sort',
                'title' => 'admin::app.admin.system.sort_order',
                'type' => 'select',
                'options' => [
                    [
                        'title' => '1',
                        'value' => 1
                    ], [
                        'title' => '2',
                        'value' => 2
                    ], [
                        'title' => '3',
                        'value' => 3
                    ], [
                        'title' => '4',
                        'value' => 4
                    ]
                ],
            ]
        ]
    ]
];