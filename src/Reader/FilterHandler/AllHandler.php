<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Reader\FilterHandler;

use Yiisoft\Data\Cycle\Reader\QueryBuilderFilterHandler;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\Iterable\Context;
use Yiisoft\Data\Reader\Iterable\IterableFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class AllHandler implements QueryBuilderFilterHandler, IterableFilterHandlerInterface
{
    #[\Override]
    public function getFilterClass(): string
    {
        return All::class;
    }

    #[\Override]
    public function getAsWhereArguments(FilterInterface $filter, array $handlers): array
    {
        return [];
    }

    #[\Override]
    public function match(object|array $item, FilterInterface $filter, Context $context): bool
    {
        return true;
    }
}
