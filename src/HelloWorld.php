<?php
declare(strict_types=1);

namespace App;

class HelloWorld
{
    public function announce(): void
    {
        echo 'Hello, autoloaded world';
    }
}