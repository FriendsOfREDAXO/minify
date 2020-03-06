<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3d2fc343ed9c4cce7b8bcbf7c491bed3
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MatthiasMullie\\PathConverter\\' => 29,
            'MatthiasMullie\\Minify\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MatthiasMullie\\PathConverter\\' => 
        array (
            0 => __DIR__ . '/..' . '/matthiasmullie/path-converter/src',
        ),
        'MatthiasMullie\\Minify\\' => 
        array (
            0 => __DIR__ . '/..' . '/matthiasmullie/minify/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3d2fc343ed9c4cce7b8bcbf7c491bed3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3d2fc343ed9c4cce7b8bcbf7c491bed3::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
