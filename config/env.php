<?php

return [
    'APP_ENV' => env('APP_ENV', 'production'),
    'HR_MAIL' => env('HR_MAIL', 'willie.romansyah@edu-all.com'),
    'HR_CC' => env('HR_CC', 'lawrence.benning@edu-all.com'),
    'CRM_AUTHORIZATION_KEY' => env('CRM_AUTHORIZATION_KEY', 'A3bsc21SjAS43AS33skS012CddFg'),
    'GOOGLE_SHEET_KEY_IMPORT' => env('GOOGLE_SHEET_KEY_IMPORT', '1xam159C7dirHCH9txq1g9xp98mDbktCBvg_clc4hgxI'),
    'CEO_CC' => env('CEO_CC', 'devi.kasih@edu-all.com'),
    'FINANCE_CC' => env('FINANCE_CC', 'emilia@edu-all.com'),
    'FINANCE_NAME' => env('FINANCE_NAME', 'Emilia'),
    'STUDENT_SUCCESS_CC' => env('STUDENT_SUCCESS_CC', 'nizzah.amalia@edu-all.com'),
    'HEAD_MENTOR_CC' => env('HEAD_MENTOR_CC', 'debora.wibianne@edu-all.com'),

    /**
     * Plink Payment Gateway Configuration
     * https://plink.co.id/
     */
    'MERCHANT_KEY_ID' => env('MERCHANT_KEY_ID', '4cfbd0dfe3924e918c7b3ee58402ac91'),
    'MERCHANT_ID' => env('MERCHANT_ID', '001742543563641'),
    'PAYMENT_API_URI' => env('PAYMENT_API_URI', 'https://api3.plink.co.id/gateway/v2'),
    'PAYMENT_WEB_URI' => env('PAYMENT_WEB_URI', 'https://secure3.plink.co.id'),
    'PAYMENT_SECRET_KEY' => env('PAYMENT_SECRET_KEY', '0831f04110b06471019f66b5'),
    'PAYMENT_BACKEND_CALLBACK_URI' => env('PAYMENT_BACKEND_CALLBACK_URI', 'https://crm.edu-all.com/api/v1/payment/callback'),
    'PAYMENT_FRONTEND_CALLBACK_URI' => env('PAYMENT_FRONTEND_CALLBACK_URI', 'https://edu-all.com'),

    /**
     * Invoice Email Configuration
     */
    'ALLIN_COMPANY' => env('ALLIN_COMPANY', 'PT. Jawara Edukasih Indonesia'),
    'ALLIN_ADDRESS' => env('ALLIN_ADDRESS', 'Jl Jeruk Kembar Blok Q9 No. 15'),
    'ALLIN_ADDRESS_DTL' => env('ALLIN_ADDRESS_DTL', 'Srengseng, Kembangan'),
    'ALLIN_CITY' => env('ALLIN_CITY', 'DKI Jakarta'),

    /**
     * 
     */
    'DIRECTOR_EMAIL' => env('DIRECTOR_EMAIL', "n.hendra@edu-all.com"),
    'DIRECTOR_NAME' => env('DIRECTOR_NAME', "Nicholas"),
    'OWNER_EMAIL' => env('OWNER_EMAIL', "devi.kasih@edu-all.com"),
    'OWNER_NAME' => env('OWNER_NAME', 'Devi Kasih'),
    'REGISTRATION_URL' => env('REGISTRATION_URL', 'https://registration.edu-all.com/form/event'),
];
