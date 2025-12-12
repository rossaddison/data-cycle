<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Reader\FilterHandler;

use Cycle\ORM\Select\QueryBuilder;
use Yiisoft\Data\Cycle\Exception\NotSupportedFilterException;
use Yiisoft\Data\Cycle\Reader\QueryBuilderFilterHandler;
use Yiisoft\Data\Reader\Filter\AndX;
use Yiisoft\Data\Reader\Iterable\Context;
use Yiisoft\Data\Reader\Iterable\IterableFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;

final class AndXHandler implements QueryBuilderFilterHandler, IterableFilterHandlerInterface
{
    #[\Override]
    public function getFilterClass(): string
    {
        return AndX::class;
    }

    #[\Override]
    public function getAsWhereArguments(FilterInterface $filter, array $handlers): array
    {
        /** @var AndX $filter */

        return [
            static function (QueryBuilder $select) use ($filter, $handlers) {
                foreach ($filter->filters as $subFilter) {
                    /**
                     * @var QueryBuilderFilterHandler|null $handler
                     */
                    $handler = $handlers[$subFilter::class] ?? null;
                    if ($handler === null) {
                        throw new NotSupportedFilterException($subFilter::class);
                    }
                    $select->andWhere(...$handler->getAsWhereArguments($subFilter, $handlers));
                }
            },
        ];
    }
    
    #[\Override]
    public function match(array|object $item, FilterInterface $filter, Context $context): bool
    {
        /** @var AndX $filter */

        foreach ($filter->filters as $subFilter) {
            $filterHandler = $context->getFilterHandler($subFilter::class);
            if (!$filterHandler->match($item, $subFilter, $context)) {
                return false;
            }
        }

        return true;
    }
}
