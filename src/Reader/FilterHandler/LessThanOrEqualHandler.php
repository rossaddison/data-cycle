<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Reader\FilterHandler;

use Yiisoft\Data\Cycle\Reader\QueryBuilderFilterHandler;
use Yiisoft\Data\Reader\Filter\LessThanOrEqual;
use Yiisoft\Data\Reader\Iterable\Context;
use Yiisoft\Data\Reader\Iterable\IterableFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class LessThanOrEqualHandler implements QueryBuilderFilterHandler, IterableFilterHandlerInterface
{
    #[\Override]
    public function getFilterClass(): string
    {
        return LessThanOrEqual::class;
    }

    #[\Override]
    public function getAsWhereArguments(FilterInterface $filter, array $handlers): array
    {
        /** @var LessThanOrEqual $filter */

        return [$filter->field, '<=', $filter->value];
    }

    #[\Override]
    public function match(array|object $item, FilterInterface $filter, Context $context): bool
    {
        /**
         * @var LessThanOrEqual $filter
         * @var float|int|string|null $itemValue
         */

        $itemValue = $context->readValue($item, $filter->field);
        $argumentValue = $filter->value;

        if ($itemValue === null) {
            return false;
        }

        return $itemValue <= $argumentValue;
    }
}
