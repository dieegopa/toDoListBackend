<?php

namespace App\OpenApi\RequestBodies;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'RegisterRequestBody')]
class RegisterRequestBody
{
    #[OA\Property(title: 'email', format: 'email')]
    private string $email;

    #[OA\Property(title: 'password', format: 'string')]
    private string $password;

    #[OA\Property(title: 'name', format: 'string')]
    private string $name;
}
