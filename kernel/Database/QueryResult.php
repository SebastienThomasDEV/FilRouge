<?php

namespace Sthom\Kernel\Database;

readonly class QueryResult
{
    public function __construct(
        private readonly string $sql,
        private readonly array $parameters = []
    ) {}

    public final function getSQL(): string
    {
        return $this->sql;
    }

    public final function getParameters(): array
    {
        return $this->parameters;
    }
}