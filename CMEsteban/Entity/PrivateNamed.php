<?php

namespace CMEsteban\Entity;

abstract class PrivateNamed extends PrivateEntity
{
    protected $name;

    // {{{ constructor
    public function __construct(User $user, $name)
    {
        parent::__construct($user);

        $this->name = $name;
    }
    // }}}
    // {{{ toString
    public function __toString()
    {
        return $this->name;
    }
    // }}}
}
