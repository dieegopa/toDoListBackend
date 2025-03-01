<?php

namespace App\OpenApi\RequestBodies;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'LoginRequestBody')]
class LoginRequestBody
{
    #[OA\Property(title: 'email', format: 'email')]
    private string $email;

    #[OA\Property(title: 'password', format: 'string')]
    private string $password;
}
