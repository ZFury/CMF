<?php
/**
 * Local Cache Configuration Override for Production environment
 *
 * This is config file to override cache options
 *
 * Created by Kovalenko Viacheslav kovalenko_v@nixsolutions.com
 */
return array(
    // config caching
    'module_listener_options' => array(
        'config_cache_enabled' => true,
        'module_map_cache_enabled' => true,
        'cache_dir' => 'data/cache/'
    ),
);
