<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfa7ea219802bccb5858c53887a005c1d
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Bsexception\\Dev\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Bsexception\\Dev\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfa7ea219802bccb5858c53887a005c1d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfa7ea219802bccb5858c53887a005c1d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
