<?php

namespace Bh\Mapper;

class NamedEntity extends Mapper
{
    // {{{ constructor
    public function __construct($controller)
    {
        $this->addField(new Field('name', 'Text', 'Name', array('required' => true)));

        parent::__construct($controller);
    }
    // }}}
}