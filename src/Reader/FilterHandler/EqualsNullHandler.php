<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Reader\FilterHandler;

use Yiisoft\Data\Cycle\Reader\QueryBuilderFilterHandler;
use Yiisoft\Data\Reader\Filter\EqualsNull;
use Yiisoft\Data\Reader\Iterable\Context;
use Yiisoft\Data\Reader\Iterable\IterableFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class EqualsNullHandler implements QueryBuilderFilterHandler, IterableFilterHandlerInterface
{
    #[\Override]
    public function getFilterClass(): string
    {
        return EqualsNull::class;
    }

    #[\Override]
    public function getAsWhereArguments(FilterInterface $filter, array $handlers): array
    {
        /** @var EqualsNull $filter */

        return [$filter->field, '=', null];
    }
    
    #[\Override]
    public function match(object|array $item, FilterInterface $filter, Context $context): bool
    {
        /** 
         * @var EqualsNull $filter
         * @var int|string|float|null $context->readValue($item, $filter->field) 
         */

        return $context->readValue($item, $filter->field) === null;
    }
}
