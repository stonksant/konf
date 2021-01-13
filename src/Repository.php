<?php

namespace Stoykov\Konf;

use ArrayAccess;
use Illuminate\Support\Arr;

class Repository implements ArrayAccess, RepositoryInterface
{
    /** @var self */
    private static $instance = null;

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Repository();
        }
    
        return self::$instance;
    }

    /**
     * Load data from an array of configuration files
     * 
     * @param array $files
     */
    public function load(array $files, ?string $directory = null): void
    {
        foreach ($files as $file) {
            $name = preg_replace('/\.[^.\\s]{3,4}$/', '', $file);

            $location = $directory ? $directory . "/" . $file : $file;
            $items = require_once $location;

            foreach ($items as $key => $value) {
                $this->set($name . "." . $key, $value);
            }
        }
    }

    /**
     * Loads configuration from a directory
     * 
     * @param string $directory
     */
    public function loadFromDir(string $directory): void
    {
        $files = scandir($directory);

        // Allow only .php files
        $files = array_filter($files, function($item) {
            return strpos($item, ".php");
        });

        $this->load($files, $directory);
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->items) ? $this->items[$key] : $default;
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return void
     */
    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            $this->items[$key] = $value;
        }
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
