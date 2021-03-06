<?php

namespace CMEsteban\Page;

class Image extends Page
{
    public function render()
    {
        $image = $this->getController()->getImage($this->getPath(1));

        if ($image->getLevel() == 0) {
            $this->redirect($image->getSrc());
        }

        $path = ltrim($image->getSrc(), '/')
        $resource = imagecreatefromjpeg($path);

        header('Content-Type: image/jpeg');

        return imagejpeg($resource);
    }
}
