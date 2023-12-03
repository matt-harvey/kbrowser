<?php

declare(strict_types=1);

namespace App;

/**
 * Represents a column in a table, sans any actual data.
 * A Column knows how to extract data from a data source for display in one column of a table; and it knows
 * what title to put in its cell of the column header.
 */
readonly class Column
{
    /**
     * @param string $header
     * @param string $key
     * @param \Closure $extractor
     */
    public function __construct(
        private string $header,
        private string $key,
        private \Closure $extractor,
    ) {
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function extract(mixed $dataSource): Cell
    {
        $value = ($this->extractor)($dataSource);
        $key = $this->getKey();
        return new Cell(contents: $value, key: $key, dataSource: $dataSource);
    }

    public static function fromJsonPath(string $header, string $jsonPath, ?string $key = null): self
    {
        $jsonKeys = \explode('.', $jsonPath);
        $extractor = function (mixed $dataSource) use ($jsonKeys): string {
            $result = $dataSource;
            foreach ($jsonKeys as $jsonKey) {
                $result = $result[$jsonKey];
            }
            return \strval($result);
        };
        return new self(header: $header, key: ($key ?? $jsonPath), extractor: $extractor);
    }
}