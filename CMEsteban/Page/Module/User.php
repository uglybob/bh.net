<?php

namespace CMEsteban\Page\Module;

use CMEsteban\Page\Module\Form\UserForm;

class User extends Form
{
    protected function prepare()
    {
        $this->form = new UserForm($this->getPage()->getPath(1));
    }
}
