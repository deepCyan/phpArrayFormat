<?php
namespace PhpArrayFormat;

use PhpArrayFormat\interfaces\ArrayFormatInterface;
use PhpArrayFormat\StrHelper\StrHelper;
use IteratorAggregate;
use JsonSerializable;

class Format implements ArrayFormatInterface, IteratorAggregate, JsonSerializable
{
    public function __construct(array $data = [])
    {
        $this->setValue($data);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        $formatArray = [];
        $allProtectedFields = $this->getAllProtected();
        foreach ($allProtectedFields as $k => $field) {
            $formatArray[$field] = $this->{$field};
        }
        return $formatArray;
    }

    public function toArrayNotNull(): array
    {
        $formatArray = [];
        $allProtectedFields = $this->getAllProtected();
        foreach ($allProtectedFields as $k => $field) {
            $v = $this->{$field};
            if (! is_null($v)) {
                $formatArray[$field] = $v;
            }
        }
        return $formatArray;
    }

    public function __call($name, $args)
    {
        $strHelper = new StrHelper();
        $methodType = substr($name, 0, 3);
        $field = $strHelper->snake(substr($name, 3));
        if ($methodType == 'get') {
            return $this->{$field} ?? null;
        }

        if ($methodType == 'set') {
            if (! property_exists($this, $field)) {
                throw new \Exception('call to undefined field: ' . $field);
            }
            $this->{$field} = $args[0] ?? null;
            return $this;
        }

        throw new \Exception('call to undefined method: ' . $name);
    }

    public function getAllProtected()
    {
        $ref = new \ReflectionClass($this);
        $proper = $ref->getProperties();
        $res = [];
        foreach ($proper as $value) {
            /* @var $value \ReflectionProperty */
            if ($value->getModifiers() == $value::IS_PROTECTED) {
                array_push($res, $value->name);
            }
        }
        return $res;
    }

    public function getAllProtectedWithType()
    {
        $ref = new \ReflectionClass($this);
        $proper = $ref->getProperties();
        $res = [];
        foreach ($proper as $value) {
            /* @var $value \ReflectionProperty */
            if ($value->getModifiers() == $value::IS_PROTECTED) {
                $_p = [];
                $_p['name'] = $value->name;
                $_p['type'] = null;
                if (! is_null($value->getType())) {
                    $_p['type'] = $value->getType()->getName();
                }
                $res[] = $_p;
            }
        }
        return $res;
    }

    public function mergeFromJson(string $json)
    {
        $data = json_decode($json, true);
        return $this->setValue($data);
    }

    private function setValue(array $data)
    {
        $strHelper = new StrHelper();
        foreach ($data as $key => $value) {
            $method = $strHelper->camel('set_' . $key);
            $this->{$method}($value);
        }
        return $this;
    }
}