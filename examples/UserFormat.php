<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PhpArrayFormat\Format;

class UserFormat extends Format
{
    protected $user_id;

    protected $user_name;

    protected $password;

    protected $email;
}
$user = [
    'user_name' => 'zhihui',
    'age' => 12,
    'password' => '123456',
    'user_id' => 1,
];

$format = new UserFormat($user);

// var_dump($format->toArray());
// var_dump($format->toArrayNotNull());
// var_dump($format->getUserId());
// $format->setEmail('test@test.com');