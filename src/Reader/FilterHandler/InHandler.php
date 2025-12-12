<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Reader\FilterHandler;

use Cycle\Database\Injection\Parameter;
use Yiisoft\Data\Cycle\Reader\QueryBuilderFilterHandler;
use Yiisoft\Data\Reader\Filter\In;
use Yiisoft\Data\Reader\Iterable\Context;
use Yiisoft\Data\Reader\Iterable\IterableFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class InHandler implements QueryBuilderFilterHandler, IterableFilterHandlerInterface
{
    #[\Override]
    public function getFilterClass(): string
    {
        return In::class;
    }

    #[\Override]
    public function getAsWhereArguments(FilterInterface $filter, array $handlers): array
    {
        /** @var In $filter */

        return [$filter->field, 'in', new Parameter($filter->values)];
    }
    
    #[\Override]
    public function match(array|object $item, FilterInterface $filter, Context $context): bool
    {
        /**
         * @var In $filter
         * @var array $itemValue
         */

        $itemValue = $context->readValue($item, $filter->field);
        $argumentValue = $filter->values;

        return in_array($itemValue, $argumentValue);
    }
}
