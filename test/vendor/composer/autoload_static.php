<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb8bcf216603028096e46470925f74987
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'VPFramework\\' => 12,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'VPFramework\\' => 
        array (
            0 => __DIR__ . '/../..' . '/../src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb8bcf216603028096e46470925f74987::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb8bcf216603028096e46470925f74987::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb8bcf216603028096e46470925f74987::$classMap;

        }, null, ClassLoader::class);
    }
}
