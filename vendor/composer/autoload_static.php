<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite3cb4fb3937f56653c11947027b38192
{
    public static $files = array (
        '6b3506aae420a85306bdef711957e0bd' => __DIR__ . '/../..' . '/src/Helper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'a' => 
        array (
            'app\\' => 4,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite3cb4fb3937f56653c11947027b38192::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite3cb4fb3937f56653c11947027b38192::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
