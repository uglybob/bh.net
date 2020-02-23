<?php

namespace CMEsteban\Page;

use CMEsteban\CMEsteban;
use CMEsteban\Page\Module\HTML;
use CMEsteban\Page\Module\Email;
use CMEsteban\Lib\Minify;

class Page
{
    // {{{ variables
    protected $controller;
    protected $title = '';
    protected $description = '';
    protected $keywords = [];
    protected $accessLevel = 0;
    protected $path;
    protected $cacheable = false;
    protected $content = [];
    // }}}

    // {{{ constructor
    public function __construct($path = [])
    {
        CMEsteban::$controller->access($this->accessLevel);

        CMEsteban::setPage($this);
        CMEsteban::setTemplate(CMEsteban::$setup->getTemplate($this));

        $this->path = $path;

        $this->hookConstructor();
    }
    // }}}

    // {{{ getPath
    public function getPath($offset = null)
    {
        $path = null;

        if (is_null($offset)) {
            $path = $this->path;
        } else {
            $path = (isset($this->path[$offset])) ? $this->path[$offset] : null;
        }

        return $path;
    }
    // }}}

    // {{{ hookConstructor
    protected function hookConstructor()
    {
    }
    // }}}
    // {{{ hookTitle
    protected function hookTitle()
    {
        return $this->title;
    }
    // }}}
    // {{{ hookHead
    protected function hookHead()
    {
    }
    // }}}

    // {{{ getTemplate
    public function getTemplate()
    {
        return CMEsteban::$template;
    }
    // }}}

    // {{{ renderStylesheets
    protected function renderStylesheets()
    {
        $rendered = '';
        $stylesheets = CMEsteban::$template->getStylesheets();

        if ($stylesheets) {
            $handles = Minify::minify('css', $stylesheets);

            foreach ($handles as $handle) {
                $rendered .= HTML::link([
                    'type' => 'text/css',
                    'rel' => 'stylesheet',
                    'href' => $handle,
                ]);
            }
        }

        return $rendered;
    }
    // }}}
    // {{{ renderScripts
    protected function renderScripts()
    {
        $rendered = '';
        $scripts = CMEsteban::$template->getScripts();

        if ($scripts) {
            $handles = Minify::minify('js', $scripts);

            foreach ($handles as $handle) {
                $rendered .= HTML::script([
                    'src' => $handle,
                ]);
            }
        }

        return $rendered;
    }
    // }}}
    // {{{ renderFavicon
    public function renderFavicon()
    {
        $favicon = CMEsteban::$template->getFavicon();

        if (!is_null($favicon)) {
            $rendered = HTML::link([
                'rel' => 'icon',
                'href' => $favicon,
                'type' => 'image/x-icon',
            ]);
        } else {
            $rendered = '';
        }

        return $rendered;
    }
    // }}}

    // {{{ addKeywords
    public function addKeywords($keywords)
    {
        $this->keywords = array_merge($this->keywords, $keywords);
    }
    // }}}
    // {{{ renderHead
    protected function renderHead()
    {
        $head = HTML::head(
            $this->hookHead() .
            HTML::title($this->hookTitle()) .
            $this->renderFavicon() .
            HTML::meta(['charset' => 'UTF-8']) .
            HTML::meta(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0']) .
            (($this->description) ? HTML::meta(['name' => 'description', 'content' => $this->description]) : '') .
            (($this->keywords) ? HTML::meta(['name' => 'keywords', 'content' => implode(',', $this->keywords)]) : '') .
            HTML::meta(['name' => 'date.rendered', 'content' => date('r')]) .
            $this->renderStylesheets() .
            $this->renderScripts()
        );

        return $head;
    }
    // }}}

    // {{{ isCacheable
    public function isCacheable()
    {
        return $this->cacheable;
    }
    // }}}

    // {{{ addContent
    public function addContent($section, $content)
    {
        if (isset($this->content[$section])) {
            $this->content[$section] .= $content;
        } else {
            $this->setContent($section, $content);
        }
    }
    // }}}
    // {{{ setContent
    public function setContent($section, $content)
    {
        $this->content[$section] = $content;
    }
    // }}}
    // {{{ getContent
    public function getContent($section = null)
    {
        $result = '';

        if (is_null($section)) {
            $result = $this->content;
        } elseif (isset($this->content[$section])) {
            $result = HTML::div(["#$section"], $this->content[$section]);
        }

        return $result;
    }
    // }}}
    // {{{ render
    public function render()
    {
        return '<!DOCTYPE html>' .
            HTML::html(
                $this->renderHead() .
                HTML::body(
                    HTML::div(['#content'], CMEsteban::$template->render())
                )
            );
    }
    // }}}

    // {{{ redirect
    public static function redirect($url)
    {
        header('Location: ' . $url);
        die();
    }
    // }}}

    // {{{ shortenString
    public static function shortenString($text, $length)
    {
        return (strlen($text) > $length) ? substr($text, 0, $length - 3) . '...' : $text;
    }
    // }}}
    // {{{ shortenUrl
    public static function shortenUrl($url)
    {
        $sites = [
            'facebook',
            'bandcamp',
            'youtube',
            'soundcloud',
            'mixcloud',
            'twitter',
            'vimeo',
            'myspace',
        ];

        $host = parse_url($url, PHP_URL_HOST);

        foreach ($sites as $site) {
            if (preg_match('/' . $site . '/i', $host)) return $site;
        }

        $short = preg_replace('/(?:https?:\/\/)?(?:www\.)?(.*)\/?$/i', '$1', $url);
        $short = preg_replace('@\/$@', '', $short);

        return $short;
    }
    // }}}
    // {{{ replaceUrl
    public static function replaceUrl($match)
    {
        $url = $match[0];
        $cleanedUrl = (preg_match("~^(?:f|ht)tps?://~i", $url)) ? $url : 'https://' . $url;

        $short = self::shortenUrl($cleanedUrl);
        $trimmed = self::shortenString($short, 30);

        return HTML::a(['href' => $cleanedUrl],  ">$trimmed");
    }
    // }}}
    // {{{ replaceEmail
    protected static function replaceEmail($match)
    {
        return new Email($match[0]);
    }
    // }}}
    // {{{ cleanLinebreaks
    public static function cleanLinebreaks($text)
    {
        $cleanRs = preg_replace("/\r/", '', $text);
        $clean = preg_replace("/\n/", HTML::br(), $cleanRs);

        return $clean;
    }
    // }}}
    // {{{ cleanText
    public static function cleanText($input)
    {
        $cleanLinks = preg_replace_callback('@(\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))))@', 'self::replaceUrl', $input);
        $cleanMails = preg_replace_callback('/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i', 'self::replaceEmail', $cleanLinks);
        $clean = self::cleanLineBreaks($cleanMails);

        return $clean;
    }
    // }}}
}
