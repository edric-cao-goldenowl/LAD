<?php

class Computer
{
    function __construct(Keyboard $keyboard, Mouse $mouse)
    {
        echo "Build a computer</br> ";
        $keyboard->build();
        $mouse->build();
    }
}

class Keyboard
{
    public function build()
    {
        echo "Builded new Keyboard</br>";
    }
}


class Mouse
{
    public function build()
    {
        echo "Builed new Mouse</br>";
    }
}

$keyboard = new Keyboard();
$mouse = new Mouse();
$a = new Computer($keyboard, $mouse);
