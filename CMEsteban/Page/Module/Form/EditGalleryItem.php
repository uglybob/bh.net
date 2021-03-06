<?php

namespace CMEsteban\Page\Module\Form;

abstract class EditGalleryItem extends EditImageTextEntity
{
    protected function create()
    {
        parent::create();

        $this->form->addNumber('Position');
    }
    protected function populate()
    {
        parent::populate();

        $values = [
            'Position' => $this->entity->getPosition(),
        ];

        $this->form->populate($values);
    }
    protected function save()
    {
        $values = $this->form->getValues();

        $this->entity->setPosition($values['Position']);

        parent::save();
    }
}
