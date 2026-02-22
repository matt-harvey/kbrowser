<?php

declare(strict_types=1);

namespace App\Html;

readonly class Cell
{
    public function __construct(
        public string $contents,
        public string $key,
        public mixed $dataSource,
        public ?string $url,
        public CellStyle $style = CellStyle::LEFT,
    ) {
    }
}