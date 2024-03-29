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

    /**
     * @throws \Exception
     */
    public function run(bool $out = true): string
    {
        if (! class_exists($this->classPath)) {
            throw new \Exception('file not found');
        }

        $class = new $this->classPath();
        if (! $class instanceof Format) {
            throw new \Exception('not instance of format');
        }
        $allProperties = $class->getAllProtectedWithType();
        $allPropertieNames = array_column($allProperties, 'name');
        $typeMap = array_column($allProperties, null, 'name');
        $methods = $this->getMethods($allPropertieNames, $typeMap);
        $this->fillMethods($methods, $allProperties);
        $notesStr = implode(PHP_EOL, $methods) . PHP_EOL;
        if ($out) {
            echo $notesStr . PHP_EOL;
        }
        return $notesStr;
    }

    private function getMethods(array $pros, array $typeMap)
    {
        $className = $this->getClassName();
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
                $res[] = sprintf('* @method %s %s', $className,  $this->strHelper->camel('set_' . $pro . '($value)'));
            } else {
                $res[] = sprintf('* @method %s %s', $className, $this->strHelper->camel('set_' . $pro . '(# $value)'));
            }
        }

        // 因为转了驼峰, 要在这里处理一下类型数据
        foreach ($res as &$m) {
            if (strpos($m, '#') !== false) {
                $_pro = str_replace('* @method '. $className .' set', '', $m);
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

    private function getClassName()
    {
        $arr = explode('\\', $this->classPath);
        return end($arr);
    }
}