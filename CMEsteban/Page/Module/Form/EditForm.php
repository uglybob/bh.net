<?php

namespace CMEsteban\Page\Module\Form;

use CMEsteban\CMEsteban;
use CMEsteban\Lib\Cache;
use CMEsteban\Page\Page;

abstract class EditForm
{
    // {{{ variables
    protected $entity;
    // }}}
    // {{{ constructor
    public function __construct($class, $id)
    {
        $this->id = $id;
        $this->class = $class;

        $this->buildForm();
    }
    // }}}

    // {{{ buildForm
    protected function buildForm()
    {
        $this->form = new \Depage\HtmlForm\HtmlForm('edit' . $this->class . $this->id, ['label' => 'save']);

        $this->loadEntity();

        $this->create();

        if ($this->entity) {
            $this->title = 'edit ' . $this->class;
        } else {
            $this->title = 'create ' . $this->class;
            $this->instantiateEntity();
        }

        $this->populate();
        $this->form->process();

        if ($this->form->validate()) {
            $this->save();
            $this->form->clearSession();
            $this->redirect();
        }
    }
    // }}}
    // {{{ create
    protected function create()
    {
    }
    // }}}
    // {{{ populate
    protected function populate()
    {
    }
    // }}}
    // {{{ save
    protected function save()
    {
        Cache::autoClear();
    }
    // }}}
    // {{{ loadEntity
    protected function loadEntity()
    {
        if ($this->id) {
            $getter = 'get' . ucfirst($this->class);
            $this->entity = CMEsteban::$controller->$getter($this->id);
        }
    }
    // }}}
    // {{{ instantiateEntity
    protected function instantiateEntity()
    {
        $classString = 'CMEsteban\\Entity\\' . $this->class;
        $this->entity = new $classString('');
    }
    // }}}
    // {{{ redirect
    protected function redirect()
    {
        Page::redirect('/table/' . $this->class);
    }
    // }}}

    // {{{ toString
    public function __toString()
    {
        return (string) $this->form;
    }
    // }}}
}
