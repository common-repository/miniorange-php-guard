<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc7b9a711168db537f2ce43d01181fa24
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PhpParser\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PhpParser\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/php-parser/lib/PhpParser',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc7b9a711168db537f2ce43d01181fa24::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc7b9a711168db537f2ce43d01181fa24::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
