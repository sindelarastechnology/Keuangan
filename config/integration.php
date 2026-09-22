<?php

/*
|--------------------------------------------------------------------------
| Integrasi dengan sistem eksternal
|--------------------------------------------------------------------------
|
| Sistem eksternal (mis. billing/keanggotaan) mengakses API manajemen
| user lewat header "X-Integration-Key". Kunci dibaca dari environment
| USER_MANAGEMENT_API_KEY.
|
*/

return [

    'user_management' => [

        /*
         * Kunci integrasi untuk API manajemen user.
         */
        'key' => env('USER_MANAGEMENT_API_KEY', null),
    ],

];
