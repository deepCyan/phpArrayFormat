<?php
namespace PhpArrayFormat\StrHelper;

class StrHelper
{
    /**
     * 蛇形转驼峰
     *
     * @param string $value
     * @return string
     */
    public function camel(string $value): string
    {
        $temp = ucwords(str_replace(['-', '_'], ' ', $value));
        return lcfirst(str_replace(' ', '', $temp));
    }

    /**
     * 驼峰转蛇形
     *
     * @param string $value
     * @return string
     */
    public function snake(string $value): string
    {
        $temp = $value;
        $temp = preg_replace('/\s+/u', '', ucwords($temp));

        return $this->lower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $temp));
    }

    public function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }
}