<?php

namespace App;

readonly class Breadcrumb
{
    public function __construct(
        public string $title,
        public ?string $url,
    ) {
    }
}