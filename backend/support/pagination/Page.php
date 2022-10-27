<?php

declare(strict_types=1);

namespace support\pagination;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

final class Page
{
    public function __construct(
        public Collection $list,
        public int $page,
        public int $total
    ) {
    }

    /**
     * @param array<string> $columns
     */
    public static function fromQuery(Builder|QueryBuilder $builder, PageNumber $page, Limit $limit, array $columns = ['*']): self
    {
        $dataBuilder = clone $builder;

        $total = ceil($builder->count() / $limit->value);

        $data = $dataBuilder->forPage($page->value, $limit->value)->get($columns);

        return new self($data, $page->value, toInt($total));
    }

    public static function empty(): self
    {
        return new self(new Collection(), 1, 0);
    }

    /** @param array<mixed> $array */
    public static function fromArray(array $array, int $page, int $size): self
    {
        return new self(
            list: collect(\array_slice($array, (0 === $page ? 0 : $page - 1) * $size, $size)),
            page: $page,
            total: toInt(ceil(\count($array) / $size))
        );
    }

    public function collapse(): self
    {
        return new self($this->list->map(fn ($i) => collapseProps($i)), $this->page, $this->total);
    }

    public function map(callable $callback): self
    {
        return new self($this->list->map($callback), $this->page, $this->total);
    }

    /**
     *@param array<string, callable> $replaces
     */
    public function replace(array $replaces): self
    {
        return new self(
            nestedReplace($this->list, $replaces),
            $this->page,
            $this->total
        );
    }

    /** @param string[] $keys */
    public function decode(array $keys): self
    {
        return new self(
            $this->list->map(fn ($m) => decodeProps($m, $keys)),
            $this->page,
            $this->total
        );
    }
}
