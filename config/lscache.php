<?php

return [
    /**
     * Enable ESI
     */
    'esi' => env('LSCACHE_ESI_ENABLED', false),

    /**
     * Default cache TTL in seconds
     */
    'default_ttl' => env('LSCACHE_DEFAULT_TTL', 0),

    /**
     * Default cache storage
     * private,no-cache,public,no-vary
     */
    'default_cacheability' => env('LSCACHE_DEFAULT_CACHEABILITY', 'no-cache'),

    /**
     * Guest only mode (Do not cache logged in users)
     */
     'guest_only' => env('LSCACHE_GUEST_ONLY', false),
    /**
        suffixes that is going to be added to TAGS
     * keys =>
     * LOCALE => adds current locale to the tag
     * USER => adds current user_id to the tag
     *
     */
    'suffix_key_format'=>"LOCALE+USER",

    /**
        enabled -> true or false
     */
    'enabled'=>true,
    /*
     * pages that is going to be excluded
     * */
    'exclude_pages'=>[
        "air_search/*" // added for test purposes, dont forget to remove
    ],
];
