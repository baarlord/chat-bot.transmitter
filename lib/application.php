<?php

namespace lib;

class Application
{
    public function run()
    {
        $this->Loader();
    }

    public function Loader()
    {
        spl_autoload_register(function ($class) {
            AutoLoader::autoload($class);
        });
    }
}