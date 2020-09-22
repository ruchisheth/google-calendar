<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit67f92ce0bca16f4915e0c22460aacf78
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PosBill\\GoogleCalendar\\Tests\\' => 29,
            'PosBill\\GoogleCalendar\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PosBill\\GoogleCalendar\\Tests\\' => 
        array (
            0 => __DIR__ . '/../..' . '/tests',
        ),
        'PosBill\\GoogleCalendar\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit67f92ce0bca16f4915e0c22460aacf78::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit67f92ce0bca16f4915e0c22460aacf78::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
