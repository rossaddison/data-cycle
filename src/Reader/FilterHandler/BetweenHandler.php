<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Reader\FilterHandler;

use Yiisoft\Data\Cycle\Reader\QueryBuilderFilterHandler;
use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\Iterable\Context;
use Yiisoft\Data\Reader\Iterable\IterableFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class BetweenHandler implements QueryBuilderFilterHandler, IterableFilterHandlerInterface
{
    #[\Override]
    public function getFilterClass(): string
    {
        return Between::class;
    }

    #[\Override]
    public function getAsWhereArguments(FilterInterface $filter, array $handlers): array
    {
        /** @var Between $filter  */

        return [$filter->field, 'between', $filter->minValue, $filter->maxValue];
    }

    #[\Override]
    public function match(array|object $item, FilterInterface $filter, Context $context): bool
    {
        /**
         * @var Between $filter
         * @var float|int|string|null $value
         */
        $value = $context->readValue($item, $filter->field);
        $min = $filter->minValue;
        $max = $filter->maxValue;

        return $value >= $min && $value <= $max;
    }
}
