<?php
return [
    'finance_options' => [
        'mosaic' => [
            [
                'apr' => 2.99,
                'dealer_fee' => 0.398,
                'monthly_ntc' => 406.00,
                'formula' => '(subtotal / (1 - dealer_fee)) / monthly_ntc'
            ],
            [
                'apr' => 3.99,
                'dealer_fee' => 0.354,
                'monthly_ntc' => 422.82,
                'formula' => '(subtotal / (1 - dealer_fee)) / monthly_ntc'
            ],
            [
                'apr' => 4.59,
                'dealer_fee' => 0.321,
                'monthly_ntc' => 428.72,
                'formula' => '(subtotal / (1 - dealer_fee)) / monthly_ntc'
            ],
            [
                'apr' => 4.99,
                'dealer_fee' => 0.304,
                'monthly_ntc' => 435.96,
                'formula' => '(subtotal / (1 - dealer_fee)) / monthly_ntc'
            ],
            [
                'apr' => 6.99,
                'dealer_fee' => 0.219,
                'monthly_ntc' => 472.34,
                'formula' => '(subtotal / (1 - dealer_fee)) / monthly_ntc'
            ],
            [
                'apr' => 7.99,
                'dealer_fee' => 0.16,
                'monthly_ntc' => 480.69,
                'formula' => '(subtotal / (1 - dealer_fee)) / monthly_ntc'
            ],
            [
                'apr' => 9.99,
                'dealer_fee' => 0.062,
                'monthly_ntc' => 508.49,
                'formula' => '(subtotal / (1 - dealer_fee)) / monthly_ntc'
            ]
        ],
        'renew_solar' => [
            [
                'apr' => 4.99,
                'total_loan_value' => 0.7087070759,
                'multiplier' => 103.3143235,
                'formula' => '(subtotal / total_loan_value) / multiplier'
            ],
            [
                'apr' => 6.49,
                'total_loan_value' => 0.7500244883,
                'multiplier' => 96.70431688,
                'formula' => '(subtotal / total_loan_value) / multiplier'
            ],
            [
                'apr' => 7.99,
                'total_loan_value' => 0.7988200469,
                'multiplier' => 91.79701843,
                'formula' => '(subtotal / total_loan_value) / multiplier'
            ],
            [
                'apr' => 9.49,
                'total_loan_value' => 0.857326178,
                'multiplier' => 88.44235327,
                'formula' => '(subtotal / total_loan_value) / multiplier'
            ]
        ]
    ]
];
