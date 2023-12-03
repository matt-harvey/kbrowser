<?php

declare(strict_types=1);

namespace App;

class Table implements \Iterator
{
    private int $position = 0;

    /** @var array<Column> */
    private array $columns = [];

    /** @var array<mixed> */
    private array $sources = [];

    public static function create(): self
    {
        return new self();
    }

    /** @return array<Column> */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /** @return array<string> */
    public function headers(): array
    {
        return \array_map(fn (Column $column) => $column->getHeader(), $this->columns);
    }

    /** @return array<Cell> */
    public function currentCells(): array
    {
        $current = $this->current();
        return \array_map(fn (Column $column) => $column->extract($current), $this->columns);
    }

    public function add(Column $column): self
    {
        $this->columns[] = $column;
        return $this;
    }

    /** @param array<mixed> */
    public function setSources(array $sources): self
    {
        $this->sources = $sources;
        return $this;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): mixed
    {
        return $this->sources[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return $this->position >= 0 && $this->position < \count($this->sources);
    }

    public function next(): void
    {
        $this->position++;
    }
}