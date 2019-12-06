<?php

namespace CMEsteban\Lib;

use CMEsteban\CMEsteban;

class Cache
{
    // {{{ get
    public static function get($index)
    {
        $name = self::getFilename($index);
        $result = false;

        if (self::validTime($name)) {
            $result = file_get_contents($name);
        }

        return $result;
    }
    // }}}
    // {{{ set
    public static function set($index, $data)
    {
        return self::store($index, $data);
    }
    // }}}

    // {{{ load
    public static function load($index)
    {
        // @TODO boilerplate
        $name = self::getFilename($index);
        $result = false;

        if (is_file($name)) {
            $result = file_get_contents($name);
        }

        return $result;
    }
    // }}}
    // {{{ store
    public static function store($index, $data)
    {
        $name = self::getFilename($index);
        file_put_contents($name, $data);
    }
    // }}}

    // {{{ list
    public static function list()
    {
        $files = glob(self::getDir() . '/*');
        $list = [];

        foreach ($files as $file) {
            $list[$file] = self::validTime($file);
        }

        return $list;
    }
    // }}}
    // {{{ clear
    public static function clear()
    {
        $success = true;
        $files = self::list();

        foreach ($files as $file => $valid) {
            $success = unlink($file) && $success;
        }

        return $success;
    }
    // }}}
    // {{{ autoClear
    public static function autoClear()
    {
        if (CMEsteban::$setup->getSettings('CacheAutoClear')) {
            self::clear();
        }
    }
    // }}}

    // {{{ validTime
    protected function validTime($file)
    {
        $timeLeft = 0;

        if (is_file($file)) {
            $timeLeft = CMEsteban::$setup->getSettings('CacheTime') - (time() - filemtime($file));
            $timeLeft = ($timeLeft > 0) ? $timeLeft : 0;
        }

        return $timeLeft;
    }
    // }}}
    // {{{ getDir
    public function getDir()
    {
        return CMEsteban::$setup->getSettings('Path') . '/CMEsteban/Cache';
    }
    // }}}
    // {{{ getFilename
    protected function getFilename($index)
    {
        return self::getDir() . "/$index";
    }
    // }}}
}
