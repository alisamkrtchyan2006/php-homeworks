<?php

spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    $prefix = 'App' . DIRECTORY_SEPARATOR;
    if (strpos($class, $prefix) === 0) {
        $class = substr($class, strlen($prefix)); 
        $parts = explode(DIRECTORY_SEPARATOR, $class);
        if (count($parts) > 1) {
            $parts[0] = strtolower($parts[0]); 
            $class = implode(DIRECTORY_SEPARATOR, $parts);
        }
        $path = __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
    } else {
        $path = __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
    }

    if (file_exists($path)) {
        require_once $path;
    }
});
