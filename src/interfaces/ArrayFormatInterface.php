<?php
namespace PhpArrayFormat\interfaces;

interface ArrayFormatInterface {
    /**
     * 将元素转为数组
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * 只取已赋值的元素
     *
     * @return array
     */
    public function toArrayNotNull(): array;
}