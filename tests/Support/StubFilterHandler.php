<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Tests\Support;

use Yiisoft\Data\Cycle\Reader\QueryBuilderFilterHandler;
use Yiisoft\Data\Reader\Iterable\Context;
use Yiisoft\Data\Reader\Iterable\IterableFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class StubFilterHandler implements QueryBuilderFilterHandler, IterableFilterHandlerInterface
{
    #[\Override]
    public function getFilterClass(): string
    {
        return StubFilter::class;
    }

    #[\Override]
    public function getAsWhereArguments(FilterInterface $filter, array $handlers): array
    {
        /** @var StubFilter $filter */
        return ['field', 'symbol', 'value'];
    }
    
    #[\Override]
    public function match(array|object $item, FilterInterface $filter, Context $context): bool
    {
        return true;
    }
}
