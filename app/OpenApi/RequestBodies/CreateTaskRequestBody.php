<?php

namespace App\OpenApi\RequestBodies;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'CreateTaskRequestBody')]
class CreateTaskRequestBody
{
    #[OA\Property(title: 'name', format: 'string')]
    private string $name;

    #[OA\Property(title: 'due_date', format: 'date')]
    private string $due_date;

    #[OA\Property(title: 'tag_id', format: 'integer')]
    private int $tag_id;
}
