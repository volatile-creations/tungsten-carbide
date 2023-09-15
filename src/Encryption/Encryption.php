<?php
declare(strict_types=1);

namespace App\Encryption;

use App\Encryption\Decrypter;
use App\Encryption\Encrypter;

interface Encryption extends Encrypter, Decrypter
{
}