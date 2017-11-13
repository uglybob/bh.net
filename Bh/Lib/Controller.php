<?php

namespace Bh\Lib;

use Bh\Entity\User;

class Controller
{
    // {{{ variables
    protected $request = null;
    protected $user = null;
    // }}}

    // {{{ getPage
    public function getPage($id)
    {
        if (is_null($id)) {
            $page = null;
        } else {
            $page = Mapper::find('Page', $id);
        }

        return $page;
    }
    // }}}
    // {{{ getPageByRequest
    public function getPageByRequest($request)
    {
        $page = null;
        $path = explode('/', $request);
        $request = $path[0];

        $handler = Mapper::findOneBy('Page', ['request' => $request]);

        if ($handler) {
            $pageClass = 'Bh\Page\\' . $handler->getPage();
            if (class_exists($pageClass)) {
                $page = new $pageClass($this, $path);
            } else {
                throw new \Bh\Exception\NotFoundException("Class does not exist: $pageClass");
            }
        } else {
            if (!($page = $this->hookGetPageByRequest($request, $path))) {
                throw new \Bh\Exception\NotFoundException("Page not found: $request");
            }
        }

        $rendered = '';

        if ($page->isCacheable() && !$this->getCurrentUser()) {
            $name = $this->getCacheFilename($path);

            if (
                is_file($name)
                && ((time() - filemtime($name)) < Setup::getSettings('CacheTime'))
            ) {
                $rendered = file_get_contents($name);
            } else {
                $rendered = $page->render();
                file_put_contents($name, $rendered);
            }
        } else {
            $rendered = $page->render();
        }

        return $rendered;
    }
    // }}}
    // {{{ hookGetPageByRequest
    public function hookGetPageByRequest($request, $path)
    {
        return null;
    }
    // }}}
    // {{{ getPages
    public function getPages()
    {
        $pages = [];

        if ($this->access(5)) {
            $pages = Mapper::findAll('Page');
        }

        return $pages;
    }
    // }}}
    // {{{ editPage
    public function editPage($page)
    {
        if ($this->access(5)) {
            if (is_null($page->getId())) {
                Mapper::save($page);
            }

            Mapper::commit();
        }
    }
    // }}}

    // {{{ getImage
    public function getImage($id)
    {
        if (is_null($id)) {
            $image = null;
        } else {
            $image = Mapper::find('Image', $id);
        }

        $this->access($image->getLevel());

        return $image;
    }
    // }}}

    // {{{ login
    public function login($name, $pass)
    {
        $result = false;
        $user = $this->getUserByName($name);

        if ($user && $user->authenticate($pass)) {
            $_SESSION['userId'] = $user->getId();
            $result = true;
        }

        return $result;
    }
    // }}}
    // {{{ logoff
    public function logoff()
    {
        unset($_SESSION['userId']);
    }
    // }}}
    // {{{ getCurrentUser
    public function getCurrentUser()
    {
        $user = null;

        if (isset($_SESSION['userId'])) {
            $user = $this->getUser($_SESSION['userId']);
        }

        return $user;
    }
    // }}}
    // {{{ access
    public function access($level)
    {
        $result = false;
        $user = $this->getCurrentUser();

        if (
            ($level === 0)
            || ($user && $user->getLevel() >= $level))
        {
            $result = true;
        } else {
            throw new \Bh\Exception\AccessException("Access denied. Minimum access level: $level.");
        }

        return $result;
    }
    // }}}

    // {{{ getUser
    public function getUser($id)
    {
        if (is_null($id)) {
            $user = null;
        } else { 
            $user = Mapper::find('User', $id);
        }

        return $user;
    }
    // }}}
    // {{{ getUserByName
    public function getUserByName($name)
    {
        return Mapper::findOneBy('User', ['name' => $name]);
    }
    // }}}
    // {{{ getUserByEmail
    public function getUserByEmail($email)
    {
        return Mapper::findOneBy('User', ['email' => strtolower($email)]);
    }
    // }}}
    // {{{ editUser
    public function editUser(User $newUser)
    {
        $result = false;
        $newId = $newUser->getId();
        $newNameUser = $this->getUserByName($newUser->getName());
        $newEmailUser = $this->getUserByEmail($newUser->getEmail());
        $currentUser = $this->getCurrentUser();

        if (
            is_null($newId)
            && !$newNameUser
            && !$newEmailUser
        ) {
            Mapper::save($newUser);
            Mapper::commit();
            $result = true;
        } elseif (
            $currentUser
            && $currentUser->getId() === $newId
            && (
                !$newNameUser
                || $newNameUser === $currentUser
            ) && (
                !$newEmailUser
                || $newEmailUser === $currentUser
            )
        ) {
            if (!$newUser->getPass()) {
                $newUser->copyPass($currentUser->getPass());
            }

            $currentUser = $newUser;
            Mapper::save($newUser);
            Mapper::commit();
            $result = true;
        }

        return $result;
    }
    // }}}

    // {{{ getCacheFilename
    protected function getCacheFilename($path)
    {
        return Setup::getSettings('Path') . 'Bh/Cache/' . implode('-', $path) . '.html';
    }
    // }}}
}
