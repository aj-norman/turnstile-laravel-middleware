<?php

return [
    /* -----------------------------------------------------------------
     |  Global Kill Switch
     | -----------------------------------------------------------------
     |
     | Controls whether the middleware is enabled or not. If set to false,
     | the middleware will not run and all requests will be allowed through.
     |
     | This can be useful for testing or for temporarily disabling the middleware
     | without having to remove it from the middleware stack if cloudflare is
     | down or not working.
     |
     |
     */
    'enabled' => env('CF_TURNSTILE_ENABLED', true),

    /* -----------------------------------------------------------------
     |  Site Key
     | -----------------------------------------------------------------
     |
     | A unique key for your site obtained from the Cloudflare dashboard
     |
     */
    'site_key' => env('CF_TURNSTILE_SITE_KEY', ''),

    /* -----------------------------------------------------------------
     |  Secret Key
     | -----------------------------------------------------------------
     |
     | A unique secret for your site obtained from the Cloudflare dashboard
     |
     */
    'secret_key' => env('CF_TURNSTILE_SECRET_KEY', ''),
];
