<?php

namespace CMEsteban\Page\Module;

use CMEsteban\CMEsteban;

class Table extends Form
{
    // {{{ constructor
    public function __construct(array $items, array $attributes = [])
    {
        parent::__construct();

        CMEsteban::$template->addStylesheet(CMEsteban::$setup->getSettings('PathCme') . '/CMEsteban/Page/css/table.css');
        $this->list = '';

        if (empty($attributes)) {
            $key = array_key_first($items);

            if (isset($items[$key])) {
                foreach ($items[$key] as $attribute => $value) {
                    $attributes[$attribute] = $attribute;
                }
            }
        }

        $header = '';
        foreach ($attributes as $attribute => $caption) {
            $header .= HTML::div(['.cthead'], $caption);
        }
        $this->list .= HTML::div(['.ctheader'], HTML::div(['.ctrow'], $header));

        foreach ($items as $item) {
            $properties = '';

            foreach ($attributes as $attribute => $caption) {
                $class = preg_replace('/[^_\-a-z0-9]/i', '_', $caption);
                $properties .= HTML::div([".$class", '.ctcell'], $item[$attribute]);
            }

            $this->list .= HTML::div(['.ctrow'], $properties);
        }

        $this->list = HTML::div(['.ctable'], $this->list);
    }
    // }}}

    // {{{ formatArray
    public function formatArray(array $input)
    {
        $result = [];

        foreach ($input as $key => $value) {
            $result[] = [$key, $value];
        }

        return $result;
    }
    // }}}
    // {{{ toString
    public function __toString()
    {
        return $this->list;
    }
    // }}}

    // {{{ shorten
    public function shorten($text, $length)
    {
        return (strlen($text) > $length) ? substr($text, 0, $length - 3) . '...' : $text;
    }
    // }}}
}
