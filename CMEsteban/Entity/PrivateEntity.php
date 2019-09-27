<?php

namespace CMEsteban\Entity;

abstract class PrivateEntity extends Entity
{
    protected $user;

    // {{{ constructor
    public function __construct($user)
    {
        parent::__construct();

        $this->user = $user;
    }
    // }}}
    // {{{ setUser
    private function setUser() {}
    // }}}
}
