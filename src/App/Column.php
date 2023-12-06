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
    private ?\Closure $urlExtractor;

    /**
     * @param string $header
     * @param string $key
     * @param \Closure $contentsExtractor
     */
    public function __construct(
        private string $header,
        private string $key,
        private \Closure $contentsExtractor,
        ?\Closure $urlExtractor = null,
    ) {
        $this->urlExtractor = ($urlExtractor ?? fn (string $context, mixed $dataSource) => null);
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function extract(string $context, mixed $dataSource): Cell
    {
        $contents = ($this->contentsExtractor)($dataSource);
        $key = $this->getKey();
        $url = ($this->urlExtractor)($context, $dataSource);
        return new Cell($contents, $key, $dataSource, $url);
    }

    public static function fromJsonPath(
        string $header,
        string $jsonPath,
        ?string $key = null,
        ?\Closure $urlExtractor = null,
    ): self
    {
        $jsonKeys = \explode('.', $jsonPath);
        $extractor = function (mixed $dataSource) use ($jsonKeys): string {
            $result = $dataSource;
            foreach ($jsonKeys as $jsonKey) {
                $result = $result[$jsonKey];
            }
            return \strval($result);
        };
        return new self(
            header: $header,
            key: ($key ?? $jsonPath),
            contentsExtractor: $extractor,
            urlExtractor: $urlExtractor,
        );
    }
}