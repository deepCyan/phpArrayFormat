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
try {
    (new \PhpArrayFormat\Command\GenComment(UserFormat::class))->run();
} catch (Exception $e) {
}