<?php
namespace PhpArrayFormat\Command;
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpArrayFormat\Format;
use PhpArrayFormat\StrHelper\StrHelper;

class GenComment
{
    protected $inputs = [];

    protected $strHelper;

    public function __construct(array $i)
    {
        $this->inputs = $i;
        $this->strHelper = new StrHelper();
    }

    public function run()
    {
        $classPath = $this->inputs[1] ?? '';
        if (empty($classPath)) {
            echo 'empty class path' . PHP_EOL;
            return;
        }
        if (! file_exists($classPath)) {
            echo 'file not found' . PHP_EOL;
            return;
        }
        require_once $classPath;
        $classNameEx = explode('/', $classPath);
        $fileName = $classNameEx[count($classNameEx) - 1];
        $className = str_replace('.php', '', $fileName);
        $class = new $className();
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
        // array_push($methods, '*');
        array_push($methods, '*/');

        // array_unshift($methods, '*');
        array_unshift($methods, '/**');
        // array_unshift($methods, PHP_EOL);
        // array_unshift($methods, 'namespace App\Format;');
        // array_unshift($methods, PHP_EOL);
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

$command = new GenComment($argv);
$command->run();