<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpArrayFormat\Format;

class UserFormat extends Format
{
    protected int $user_id = 0;

    protected string $user_name = '';

    protected string $password = '';

    protected string $email = '';

    protected int $age = 0;
}
$user = [
    'user_name' => 'zhihui',
    'age' => 12,
    'password' => '123456',
    'user_id' => 1,
];

$format = new UserFormat($user);
var_dump($format->toArray());
var_dump($format->toArrayNotNull());
var_dump($format->getUserId());
$format->setEmail('test@test.com');
var_dump($format->getEmail());

echo '--------------' . PHP_EOL;

$format2 = new UserFormat();
$json = json_encode($user);
$format2->mergeFromJson($json);
var_dump($format2->getPassword());
$format2->setPassword('111ppp');
var_dump($format2->getPassword());
var_dump($format2->getUserId());
$format2->setUserId(99);
var_dump($format2->getUserId());

echo $format2 . PHP_EOL;