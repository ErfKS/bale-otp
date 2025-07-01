<?php

return [
    'username' => env('BALE_OTP_USERNAME'),
    'password' => env('BALE_OTP_PASSWORD'),
    'url' => env('BALE_OTP_URL','https://safir.bale.ai/api/v2'),


    /*
   |--------------------------------------------------------------
   | مقادیر ویژهٔ تست
   |--------------------------------------------------------------
   |   در تست‌ها از این بخش استفاده می‌کنیم تا نیاز به set کردن
   |   دستی کانفیگ در متد defineEnvironment نباشد
   */
    'tests' => [
        'phone' => env('BALE_OTP_TEST_PHONE', '09123456789'),
        'code'  => env('BALE_OTP_TEST_CODE', 4321),
    ],
];
