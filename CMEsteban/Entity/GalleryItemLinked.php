<?php

namespace CMEsteban\Entity;

abstract class GalleryItemLinked extends GalleryItem
{
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
}
