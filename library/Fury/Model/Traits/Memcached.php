<?php
/**
 * Trait for work with memcached
 * Date: 24.12.2014
 *
 * @NOTE: To include getCacheService function into $this
 * you have to 'use' this trait
 *
 * Created by Kovalenko Viacheslav kovalenko_v@nixsolutions.com
 */

namespace Fury\Model\Traits;

trait Memcached
{

    /**
     * @var \Zend\Cache\Storage\Adapter\Memcached
     */
    protected $memcached = null;

    /**
     * Get memcached service
     * @return \Zend\Cache\Storage\Adapter\Memcached
     */
    public function getCacheService()
    {
        $memcached = $this->memcached;
        if (empty($memcached)) {
            /**
             * @var \Zend\Cache\Storage\Adapter\Memcached $memcached
             */
            $memcached = $this->getServiceLocator()->get('memcached');
        }

        return $memcached;
    }
}
