<?php

namespace CMEsteban\Page;

use CMEsteban\CMEsteban;

class Home extends Page
{
    protected $cacheable = true;

    // {{{ hookConstructor
    protected function hookConstructor()
    {
        parent::hookConstructor();

        $user = CMEsteban::$controller->getCurrentUser();

        CMEsteban::$template->addContent('main', "hi $user :)");
    }
    // }}}
}
