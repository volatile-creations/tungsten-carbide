<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class RsvpDto
{
    public function __construct(
        #[Assert\All(
            new Assert\All(
                new Assert\Uuid(message: 'Invalid guest ID presented')
            )
        )]
        /**
         * @param array<string,string[]> $events
         */
        public array $events
    ) {
    }
}