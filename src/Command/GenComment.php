<?php
namespace PhpArrayFormat\Command;

use PhpArrayFormat\Format;
use PhpArrayFormat\StrHelper\StrHelper;

class GenComment
{
    protected string $classPath;

    protected StrHelper $strHelper;

    public function __construct(string $classPath)
    {
        $this->classPath = $classPath;
        $this->strHelper = new StrHelper();
    }

    public function run()
    {
        if (! class_exists($this->classPath)) {
            echo 'file not found' . PHP_EOL;
            return;
        }

        $class = new $this->classPath();
        if (! $class instanceof Format) {
            echo 'not instance of format' . PHP_EOL;
            return;
        }
        $allProperties = $class->getAllProtectedWithType();
        $allPropertieNames = array_column($allProperties, 'name');
        $typeMap = array_column($allProperties, null, 'name');
        $methods = $this->getMethods($allPropertieNames, $typeMap);
        $this->fillMethods($methods, $allProperties);
        $notesStr = implode(PHP_EOL, $methods) . PHP_EOL;
        echo $notesStr . PHP_EOL;
    }

    private function getMethods(array $pros, array $typeMap)
    {
        $res = [];
        // getter
        foreach ($pros as $key => $pro) {
            if (is_null($typeMap[$pro]['type'])) {
                $res[] = '* @method ' . $this->strHelper->camel('get_' . $pro . '()');
            } else {
                $res[] = '* @method ' . $typeMap[$pro]['type'] . ' ' . $this->strHelper->camel('get_' . $pro . '()');
            }
        }

        $res[] = '*';

        // setter
        foreach ($pros as $key => $pro) {
            if (is_null($typeMap[$pro]['type'])) {
                $res[] = '* @method ' . $this->strHelper->camel('set_' . $pro . '($value)');
            } else {
                $res[] = '* @method ' . $this->strHelper->camel('set_' . $pro . '(# $value)');
            }
        }
        // 因为转了驼峰, 要在这里处理一下类型数据
        foreach ($res as &$m) {
            if (strpos($m, '#') !== false) {
                $_pro = str_replace('* @method set', '', $m);
                $_pro = str_replace('(#$value)', '', $_pro);
                $_pro = $this->strHelper->snake($_pro);
                $m = str_replace('#', $typeMap[$_pro]['type'] . ' ', $m);
            }
        }

        return $res;
    }

    private function fillMethods(&$methods)
    {
        $methods[] = '*/';
        array_unshift($methods, '/**');
    }

    private function readFile(string $filePath)
    {
        $file = fopen($filePath, 'a+');
        $content = fread($file, filesize($filePath));
        fclose($file);
        return $content;
    }

    private function rewriteFormat($filePath, $newFile)
    {
        $file = fopen($filePath, 'w+');
        fwrite($file, $newFile);
        fclose($file);
    }
}