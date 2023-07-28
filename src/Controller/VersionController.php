<?php

namespace App\Controller;

use App\Kernel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/version',
    name: 'version',
    host: 'localhost',
    methods: 'GET'
)]
final readonly class VersionController
{
    public function __invoke(): Response
    {
        return new JsonResponse(
            [
                'symfony' => Kernel::VERSION,
                'php' => phpversion()
            ]
        );
    }
}