<?php

namespace Bh\Mapper;

use \Bh\Exceptions\DataException;

abstract class Mapper
{
    // {{{ variables
    protected $controller = null;
    protected $pdo = null;
    protected $class = null;
    protected $fields = array();
    // }}}
    // {{{ constructor
    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->pdo = $this->controller->getPdo();
        $this->class = $this->getClass();
    }
    // }}}

    // {{{ addField
    public function addField($field)
    {
        $this->fields[] = $field;
    }
    // }}}

    // {{{ getClass
    public function getClass()
    {
        $classNameArray = explode('\\', get_class($this));
        return end($classNameArray);
    }
    // }}}
    // {{{ getFields
    public function getFields()
    {
        return $this->fields;
    }
    // }}}
    // {{{ getColumns
    protected function getColumns()
    {
        $columns = array();
        foreach ($this->getFields() as $field) {
            $columns[] = $field->getColumn();
        }
        return $columns;
    }
    // }}}

    // {{{ save
    public function save($object)
    {
        $columns = $this->getColumns();

        if ($object->id) {
            $setString = '';

            foreach ($columns as $column) {
                $setString .= $column . ' = :' . $column . ', ';
            }

            $setString = substr($setString, 0, -2 ) . ' ';
            $sql = 'UPDATE ' . $this->class . ' SET ' . $setString . 'WHERE id = :id';
            $statement = $this->pdo->prepare($sql);

            $statement->bindParam('id', $object->id);

            foreach($columns as $column) {
                $statement->bindParam($column, $object->$column);
            }

            if (!$statement->execute()) {
                throw new DataException(implode($statement->errorInfo()));
            }
        } else {
            $sql = 'INSERT INTO ' . $this->class . ' (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', $columns) . ')';
            $statement = $this->pdo->prepare($sql);

            foreach($columns as $column) {
                $statement->bindParam($column, $object->$column);
            }

            if (!$statement->execute()) {
                throw new DataException(implode($statement->errorInfo()));
            }

            $object->id = $this->pdo->lastInsertId();
        }

        return $object->id;
    }
    // }}}
    // {{{ delete
    public function delete($object)
    {
        $sql = ('DELETE FROM ' . $this->class . ' WHERE id = :id');
        $statement = $this->pdo->prepare($sql);

        $statement->bindParam('id', $object->id);
        $statement->execute();
    }
    // }}}
    // {{{ load
    public function load($id)
    {
        $objects = $this->getAllWhere(array('id' => $id));

        if (
            count($objects) === 1
            && isset($objects[0])
        ) {
            $loaded = $objects[0];
        } else {
            // @todo exception on > 1
            $loaded = false;
        }

        return $loaded;
    }
    // }}}
    // {{{ getAllWhere
    public function getAllWhere($conditions = array())
    {
        $whereString = '';

        if (!empty($conditions)) {
            foreach ($conditions as $column => $value) {
                $whereString .= $column . ' = :' . $column . ' AND ';
            }
            $whereString = ' WHERE ' . substr($whereString, 0, -5 );
        }

        $query = 'SELECT id,timestamp,' . implode(',', $this->getColumns()) . ' FROM ' . $this->class . $whereString;
        $statement = $this->pdo->prepare($query);

        foreach($conditions as $column => $value) {
            $statement->bindParam($column, $value);
        }

        $statement->execute();
        $results = $statement->fetchAll(\PDO::FETCH_CLASS, $this->controller->getClass('Entity', $this->class));

        return $results;
    }
    // }}}
    // {{{ getAll
    public function getAll()
    {
        return $this->getAllWhere();
    }
    // }}}
}