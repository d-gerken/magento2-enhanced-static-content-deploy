<?php

namespace Dgerken\EnhancedStaticContentDeploy\Cache;


abstract class AbstractCache
{
    /**
     * @var $instance reference to instance of this class
     */
    protected static $instances;

    /**
     * Storage
     * @var array $storage
     */
    protected $storage = [];

    /**
     * Returns the instance of this class.
     *
     * @return $this.
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }

    /**
     * Protected constructor to prevent creating a new instance of this class
     */
    protected function __construct()
    {
    }

    /**
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * @return void
     */
    private function __wakeup()
    {
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function get($key)
    {
        if(isset($this->storage[$key]))
            return $this->storage[$key];
        return null;
    }

    /**
     * Add key value pair to cache
     * @param $key
     * @param $value
     */
    public function add($key, $value)
    {
        $this->storage[$key] = $value;
    }
}