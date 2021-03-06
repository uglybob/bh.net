<?php

namespace CMEsteban\Page\Template;

use CMEsteban\Page\Module\Menu;

class CME extends Template
{
    public function __construct()
    {
        parent::__construct();

        $this->addStylesheet('/CMEsteban/Page/css/cme.css', true);
        $this->addStylesheet('/CMEsteban/Page/css/cme-layout.css', true);
        $this->addStylesheet('/CMEsteban/Page/css/cme-colors.css', true);

        $user = $this->getController()->getCurrentUser();
        $name = '';

        if ($user) {
            $name = $user->getName();
            $links = [
                'home' => '/',
                'texts' => '/table/Text',
                'images' => '/table/Image',
                'cache' => '/cache',
                $name => '/user',
                'logout' => '/login',
            ];
        } else {
            $links = [
                'home' => '/',
                'login' => '/login',
            ];

            if ($this->getSetup()->getSettings('EnableRegistration')) {
                $links['register'] = '/user';
            }
        }

        $menu = new Menu($links);

        $this->getPage()->addContent('header', $menu);
    }
}
