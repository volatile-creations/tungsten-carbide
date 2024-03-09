<?php

namespace App;

use Override;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public const string TIMEZONE = 'Europe/Amsterdam';

    use MicroKernelTrait;

    #[Override] public function boot(): void
    {
        date_default_timezone_set(self::TIMEZONE);
        parent::boot();
    }
}
