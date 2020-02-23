<?php

namespace CMEsteban\Entity;

/**
 * @MappedSuperclass
 **/
abstract class AbstractText extends Named
{
    /**
     * @Column(type="text", nullable=true)
     **/
    protected $text;
    /**
     * @Column(type="string", unique=true, nullable=true)
     **/
    protected $link;

    // {{{ getHeading
    public static function getHeadings()
    {
        $headings = parent::getHeadings();
        $headings[] = 'Link';

        return $headings;
    }
    // }}}
    // {{{ getRow
    public function getRow()
    {
        $rows = parent::getRow();
        $rows[] = $this->getLink();

        return $rows;
    }
    // }}}

    // {{{ getFormattedText
    public function getFormattedText($createAnchors = true)
    {
        return self::format($this->getText(), $createAnchors);
    }
    // }}}
    // {{{ format
    public static function format($text, $createAnchors)
    {
        $result = '';

        if ($text) {
            $result = new \CMEsteban\Page\Module\Text($text, $createAnchors);
        }

        return $result;
    }
    // }}}
}
