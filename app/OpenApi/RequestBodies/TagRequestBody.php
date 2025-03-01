<?php

namespace App\OpenApi\RequestBodies;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'TagRequestBody')]
class TagRequestBody
{
    #[OA\Property(title: 'name', format: 'string')]
    private string $name;
}
