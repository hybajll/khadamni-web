<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {
        if (\PHP_OS_FAMILY !== 'Windows') {
            return parent::getCacheDir();
        }

        $base = rtrim(sys_get_temp_dir(), '\\/').DIRECTORY_SEPARATOR.'khadamni';

        return $base.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.$this->environment;
    }

    public function getLogDir(): string
    {
        if (\PHP_OS_FAMILY !== 'Windows') {
            return parent::getLogDir();
        }

        $base = rtrim(sys_get_temp_dir(), '\\/').DIRECTORY_SEPARATOR.'khadamni';

        return $base.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR.$this->environment;
    }
}
