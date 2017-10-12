<?php

namespace Bh\Page;

use Bh\Page\Module\HTML;

class Template
{
    // {{{ variables
    protected $stylesheets = [];
    protected $favicon = null;
    // }}}

    // {{{ getStylesheets
    public function getStylesheets()
    {
        return $this->stylesheets;
    }
    // }}}

    // {{{ head
    public function head($head)
    {
        return $head;
    }
    // }}}
    // {{{ favicon
    public function favicon()
    {
        if (!is_null($this->favicon)) {
            $rendered = HTML::link([
                'rel' => 'shortcut icon',
                'href' => $this->favicon,
                'type' => 'image/vnd.microsoft.icon',
            ]);
        } else {
            $rendered = '';
        }

        return $rendered;
    }
    // }}}
}
