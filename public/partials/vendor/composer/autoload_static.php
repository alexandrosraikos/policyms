<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3b7126deffa564d9e68d3465b4bbc289
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3b7126deffa564d9e68d3465b4bbc289::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3b7126deffa564d9e68d3465b4bbc289::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3b7126deffa564d9e68d3465b4bbc289::$classMap;

        }, null, ClassLoader::class);
    }
}
