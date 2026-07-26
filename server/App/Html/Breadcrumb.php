<?php

declare(strict_types=1);

namespace App\Html;

readonly class Breadcrumb
{
    public function __construct(
        public string $title,
        public ?string $url,
    ) {
    }
}
