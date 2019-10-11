<?php

namespace CMEsteban\Page;

use CMEsteban\Page\Module\DeleteEntity;

class Delete extends Backend
{
    // {{{ hookConstructor
    public function hookConstructor()
    {
        parent::hookConstructor();

        $this->deleteModule = new DeleteEntity($this->controller, $this);
    }
    // }}}
    // {{{ renderContent
    public function renderContent()
    {
        return parent::renderContent() . $this->deleteModule;
    }
    // }}}
}
