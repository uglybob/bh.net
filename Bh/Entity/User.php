<?php

namespace Bh\Entity;

class User extends Entity
{
    protected $email;
    protected $pass;
    protected $level;

    // {{{ constructor
    public function __construct()
    {
        parent::__construct();

        $this->level = 0;
    }
    // }}}

    // {{{ getPass
    protected function getPass()
    {
    }
    // }}}
    // {{{ setPass
    public function setPass($pass)
    {
        $this->pass = $this->hash($pass);
    }
    // }}}

    // {{{ hash
    protected function hash($pass)
    {
        return sha1($pass);
    }
    // }}}
    // {{{ authenticate
    public function authenticate($pass)
    {
        return $this->hash($pass) == $this->pass;
    }
    // }}}
}