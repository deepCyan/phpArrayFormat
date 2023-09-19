<?php
namespace PhpArrayFormat\Command;

use PhpArrayFormat\Format;
use PhpArrayFormat\StrHelper\StrHelper;

class GenComment
{
    protected $classPath;

    protected $strHelper;

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
        $allProperties = $class->getAllProtected();
        $methods = $this->getMethods($allProperties);
        $this->fillMethods($methods);
        $notesStr = implode(PHP_EOL, $methods) . PHP_EOL;
        echo $notesStr . PHP_EOL;
        // $classFile = $this->readFile($classPath);
        // $p = '/<?php([\w\W]*)class/i';
        // preg_match_all($p, $classFile, $match);
        // var_dump($match[1][0]);
        // $newFile = str_replace($match[1][0], $notesStr, $classFile);
        // var_dump($newFile);
        // $this->rewriteFormat($classPath, $newFile);
    }

    private function getMethods(array $pros)
    {
        $res = [];
        // getter
        foreach ($pros as $key => $pro) {
            $res[] = '* @method ' . $this->strHelper->camel('get_' . $pro . '()');
        }

        array_push($res, '*');

        // setter
        foreach ($pros as $key => $pro) {
            $res[] = '* @method ' . $this->strHelper->camel('set_' . $pro . '($value)');
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