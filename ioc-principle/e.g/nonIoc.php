<?php

class A
{
    protected $b;
    function __construct(B $b)
    {
        $this->b = $b;
    }

    public function callAll()
    {
        $this->b->call();
    }
}

class B
{
    private string $name;
    private int $age;
    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }
    public function call()
    {
        echo "Hello {$this->name}, {$this->age} year old. This is function call() of Class B</br>";
    }
}

class C
{
    public function call()
    {
        echo "This is function call() of Class C</br>";
    }
}
$b = new B('Edric', 5);
$a = new A($b);
$a->callAll();
