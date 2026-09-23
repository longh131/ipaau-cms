<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Newsletter notification recipient (fallback)
    |--------------------------------------------------------------------------
    |
    | Submissions are emailed to 系统设置 → 网站信息 → 联系邮箱.
    | NEWSLETTER_TO is used only when that field is empty.
    |
    */

    'notification_to' => env('NEWSLETTER_TO'),

];
