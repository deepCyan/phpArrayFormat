<?php
namespace PhpArrayFormat;

use PhpArrayFormat\interfaces\ArrayFormatInterface;
use PhpArrayFormat\StrHelper\StrHelper;

class Format implements ArrayFormatInterface
{
    public function __construct(array $data = [])
    {
        $strHelper = new StrHelper();
        foreach ($data as $key => $value) {
            $method = $strHelper->camel('set_' . $key);
            $this->{$method}($value);
        }
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
        if (substr($name, 0, 3) == 'get') {
            $field = $strHelper->snake(substr($name, 3));

            return $this->{$field} ?? null;
        }

        if (substr($name, 0, 3) == 'set') {
            $field = $strHelper->snake(substr($name, 3));
            if (property_exists($this, $field)) {
                $this->{$field} = $args[0] ?? null;
            }

            return $this;
        }

        throw new \Exception('call to undefined method: ' . $name);
    }

    public function getAllProtected()
    {
        $ref = new \ReflectionClass($this);
        $propers = $ref->getProperties();
        $res = [];
        foreach ($propers as $key => $value) {
            /* @var $value \ReflectionProperty */
            if ($value->getModifiers() == $value::IS_PROTECTED) {
                array_push($res, $value->name);
            }
        }
        return $res;
    }
}