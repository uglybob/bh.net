<?php

namespace CMEsteban\Entity;

/**
 * @MappedSuperclass
 **/
abstract class GalleryItem extends ImageEntity
{
    /**
     * @Column(type="integer")
     **/
    protected $position;
    /**
     * @Column(type="text", nullable=true)
     **/
    protected $text;

    // {{{ getHeading
    public static function getHeadings()
    {
        return [
            'Name',
            'Position',
        ];
    }
    // }}}
    // {{{ getRow
    public function getRow()
    {
        return [
            \CMEsteban\Page\Module\Text::shortenString($this->getName(), 30),
            $this->getPosition(),
        ];
    }
    // }}}

    // {{{ getFormattedText
    public function getFormattedText()
    {
        return AbstractText::format($this->getText());
    }
    // }}}
}
