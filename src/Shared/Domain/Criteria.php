<?php

declare(strict_types=1);

namespace App\Shared\Domain;

class Criteria
{
    public $filters = [];

    public function __construct(array $filters = []) {}

    public static function createFromSearchRequest(SearchRequest $request): self
    {
        return new self($request->getFilters());
    }
}