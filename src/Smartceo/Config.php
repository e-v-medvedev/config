<?php
namespace Smartceo;

class Config {

    private static $_config = null;
    private static $_instance = null;
    private $_separator = "/";

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    private function __wakeup() {
        
    }

    public static function getInstance() {
        if (self::$_instance != null) {
            return self::$_instance;
        }
        return new self;
    }

    /**
     * 
     * @param string $separator
     */
    public function setSeparator ($separator) {
        $this->_separator = $separator;
    }
    
    /**
     * 
     * @param string $dir
     */
    public function loadFromDir($dir) {
        $di = new RecursiveDirectoryIterator($dir);

        if (!isset($dataFile)) {
            $dataFile = array();
        }

        //сканирование всех поддиректорий, выборка индексных файлов
        // и слияние их в одни массив данных
        foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
            if ($file->isDir())
                continue;

            $dataFile[] = $filename;
        }
        
        foreach ($dataFile as $file) {
            $data = include $file;
            $this->load($data);
        }

    }

    public function load(array $cfg) {
        if (self::$_config == null) {
            self::$_config = $cfg;
        } else {
            self::$_config = array_merge(self::$_config, $cfg);
        }
    }

    /**
     * @param array $array
     * @param array|string $parents
     * @param string $glue
     * @return mixed
     */
    public function get($parents) {
        
        if (!is_array($parents)) {
            $parents = explode($this->_separator, $parents);
        }

        $ref = &self::$_config;

        foreach ((array) $parents as $parent) {
            if (is_array($ref) && array_key_exists($parent, $ref)) {
                $ref = &$ref[$parent];
            } else {
                return null;
            }
        }
        return $ref;
    }

    /**
     * @param array $array
     * @param array|string $parents
     * @param mixed $value
     * @param string $glue
     */
    public function set($parents, $value) {
        if (!is_array($parents)) {
            $parents = explode($this->_separator, (string) $parents);
        }

        $ref = &self::$_config;

        foreach ($parents as $parent) {
            if (isset($ref) && !is_array($ref)) {
                $ref = array();
            }

            $ref = &$ref[$parent];
        }

        $ref = $value;
    }

    /**
     * @param array $array
     * @param array|string $parents
     * @param string $glue
     */
    public function unsetValue($parents, &$array = null) {
        if (!is_array($parents)) {
            $parents = explode($this->_separator, $parents);
        }
        
        if ($array == null) {
            $array = &self::$_config;
        }

        $key = array_shift($parents);

        if (empty($parents)) {
            unset($array[$key]);
        } else {
            $this->unsetValue($parents, $array[$key]);
        }
    }

}
