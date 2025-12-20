<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Reader;

use Closure;
use Cycle\Database\Query\SelectQuery;
use Cycle\ORM\Select;
use Cycle\ORM\Select\QueryBuilder;
use Generator;
use InvalidArgumentException;
use Yiisoft\Data\Cycle\Exception\NotSupportedFilterException;
use Yiisoft\Data\Cycle\Reader\FilterHandler\LikeHandler\LikeHandlerFactory;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\Iterable\Context;
use Yiisoft\Data\Reader\Iterable\IterableFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Cycle\Reader\FilterHandler\AllHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\AndXHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\NoneHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\OrXHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\BetweenHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\EqualsHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\EqualsNullHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\GreaterThanHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\GreaterThanOrEqualHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\InHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\LessThanHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\LessThanOrEqualHandler;
use Yiisoft\Data\Cycle\Reader\FilterHandler\NotHandler;
use Yiisoft\Data\Reader\Iterable\ValueReader\FlatValueReader;
use Yiisoft\Data\Reader\Iterable\ValueReader\ValueReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Cycle\Reader\Cache\CachedCollection;
use Yiisoft\Data\Cycle\Reader\Cache\CachedCount;

/**
 * @template TKey as array-key
 * @template TValue as array|object
 *
 * @implements DataReaderInterface<TKey, TValue>
 */
final class EntityReader implements DataReaderInterface
{
    private Select|SelectQuery $query;
    /**
     * @psalm-var non-negative-int|null
     */
    private ?int $limit = null;
    private int $offset = 0;
    private Context $context;
    private ?Sort $sorting = null;
    private FilterInterface $filter;
    private CachedCount $countCache;
    private CachedCollection $itemsCache;
    private CachedCollection $oneItemCache;

    private array $filterHandlers;

    /**
     * @param Select|SelectQuery $query
     * @param ValueReaderInterface $valueReader
     * @param IterableFilterHandlerInterface[] $extraFilterHandlers
     */
    public function __construct(
        Select|SelectQuery $query,
        ValueReaderInterface $valueReader = new FlatValueReader(),
        array $extraFilterHandlers = [],
    ) {
        $this->query = clone $query;
        $this->countCache = new CachedCount($this->query);
        $this->itemsCache = new CachedCollection();
        $this->oneItemCache = new CachedCollection();
        /**
         * @psalm-suppress InternalMethod There is no other way to get driver for SelectQuery.
         * @psalm-suppress UndefinedMagicMethod The magic method is not defined in annotations.
         */
        $likeHandler = LikeHandlerFactory::getLikeHandler($this->query->getDriver()?->getType() ?? 'SQLite');
        $this->filterHandlers = $this->prepareFilterHandlers([
            new AllHandler(),
            new NoneHandler(),
            new AndXHandler(),
            new OrXHandler(),
            new BetweenHandler(),
            new EqualsHandler(),
            new EqualsNullHandler(),
            new GreaterThanHandler(),
            new GreaterThanOrEqualHandler(),
            new InHandler(),
            new LessThanHandler(),
            new LessThanOrEqualHandler(),
            $likeHandler,
            new NotHandler(),
            ...$extraFilterHandlers,
        ]);
        $this->context = new Context($this->filterHandlers, $valueReader);
        $this->filter = new All();
    }

    #[\Override]
    public function getSort(): ?Sort
    {
        return $this->sorting;
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function withLimit(?int $limit): static
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if ($limit < 0) {
            throw new InvalidArgumentException('$limit must not be less than 0.');
        }
        $new = clone $this;

        if ($new === $this) {
            throw new \RuntimeException('Query was not properly cloned!');
        }

        if ($new->limit !== $limit) {
            $new->limit = $limit;
            $new->itemsCache = new CachedCollection();
        }
        return $new;
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function withOffset(int $offset): static
    {
        $new = clone $this;

        if ($new === $this) {
            throw new \RuntimeException('Query was not properly cloned!');
        }

        if ($new->offset !== $offset) {
            $new->offset = $offset;
            $new->itemsCache = new CachedCollection();
        }
        return $new;
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function withSort(?Sort $sort): static
    {
        $new = clone $this;

        if ($new === $this) {
            throw new \RuntimeException('Query was not properly cloned!');
        }

        if ($new->sorting !== $sort) {
            $new->sorting = $sort;
            $new->itemsCache = new CachedCollection();
            $new->oneItemCache = new CachedCollection();
        }
        return $new;
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function withFilter(FilterInterface $filter): static
    {
        $new = clone $this;

        if ($new === $this) {
            throw new \RuntimeException('Query was not properly cloned!');
        }

        if ($new->filter !== $filter) {
            $new->filter = $filter;
            $new->itemsCache = new CachedCollection();
            $new->oneItemCache = new CachedCollection();
            /** @psalm-suppress ImpureMethodCall */
            $new->resetCountCache();
        }
        return $new;
    }

    #[\Override]
    public function count(): int
    {
        return $this->countCache->getCount();
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     * @return iterable
     */
    #[\Override]
    public function read(): iterable
    {
        if ($this->itemsCache->getCollection() === null) {
            $query = $this->buildSelectQuery();
            $this->itemsCache->setCollection($query->fetchAll());
        }
        return $this->itemsCache->getCollection();
    }

    #[\Override]
    public function readOne(): null|array|object
    {
        if (!$this->oneItemCache->isCollected()) {
            /** @var array|object|null $item */
            $item = $this->itemsCache->isCollected()
                // get the first item from a cached collection
                ? $this->itemsCache->getGenerator()->current()
                // Option 1: read data with limit 1: use $this->withLimit(1)->getIterator()->current();
                // Option 2: less efficient
                : $this->getIterator()->current();
            $this->oneItemCache->setCollection($item === null ? [] : [$item]);
        }
        /**
         * @psalm-suppress MixedReturnStatement
         */
        return $this->oneItemCache->getGenerator()->valid() ?
                $this->oneItemCache->getGenerator()->current()
                : null;
    }

    /**
     * Get Iterator without caching
     */
    #[\Override]
    public function getIterator(): Generator
    {
        yield from $this->itemsCache->getCollection() ?? $this->buildSelectQuery()->getIterator();
    }

    public function getSql(): string
    {
        $query = $this->buildSelectQuery();
        return (string) ($query instanceof Select ? $query->buildQuery() : $query);
    }

    /**
     * Prepare Context's first argument of filterHandlers which expects
     * array<string, IterableFilterHandlerInterface>
     *
     * @psalm-return array<string, IterableFilterHandlerInterface> $filterHandlers
     */
    private function prepareFilterHandlers(array $filterHandlers): array
    {
        $result = [];
        /**
         * @var IterableFilterHandlerInterface $filterHandler
         */
        foreach ($filterHandlers as $filterHandler) {
            $result[$filterHandler->getFilterClass()] = $filterHandler;
        }
        return $result;
    }

    private function buildSelectQuery(): SelectQuery|Select
    {
        $newQuery = clone $this->query;
        if ($this->offset >= 0 && $this->offset !== 0) {
            $newQuery->offset($this->offset);
        }
        if ($this->sorting !== null) {
            $newQuery->orderBy($this->normalizeSortingCriteria($this->sorting->getCriteria()));
        }
        if ($this->limit !== null) {
            $newQuery->limit($this->limit);
        }
        if (!($this->filter instanceof All)) {
            $newQuery->andWhere($this->makeFilterClosure($this->filter));
        }
        return $newQuery;
    }

    private function makeFilterClosure(FilterInterface $filter): Closure
    {
        return function (QueryBuilder $select) use ($filter) {
            if (!array_key_exists($filter::class, $this->filterHandlers)) {
                throw new NotSupportedFilterException($filter::class);
            }
            /** @var QueryBuilderFilterHandler $handler */
            $handler = $this->filterHandlers[$filter::class];
            $arguments = $handler->getAsWhereArguments($filter, $this->filterHandlers);
            $select->where(...$arguments);
        };
    }

    private function resetCountCache(): void
    {
        $newQuery = clone $this->query;

        // Ensure the clone worked: a clone is never identical to the original: different instances
        if ($newQuery === $this->query) {
            throw new \RuntimeException('Query was not properly cloned; $newQuery and $this->query are the same instance!');
        }

        if (!$this->filter instanceof All) {
            $newQuery->andWhere($this->makeFilterClosure($this->filter));
        }
        $this->countCache = new CachedCount($newQuery);
    }

    /**
    * @psalm-param array<string, int|string> $criteria
    * @psalm-return array<string, 'ASC'|'DESC'|string>
    * @return array<string, string>
    */
    private function normalizeSortingCriteria(array $criteria): array
    {
        foreach ($criteria as $field => $direction) {
            if (is_int($direction)) {
                /** @var 'ASC'|'DESC' $direction */
                $direction = match ($direction) {
                    SORT_DESC => 'DESC',
                    default => 'ASC',
                };
            }
            /** @var 'ASC'|'DESC'|string $direction */
            $criteria[$field] = $direction; // Always string!
        }

        /** @var array<string, string> $criteria */
        return $criteria;
    }

    #[\Override]
    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    #[\Override]
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    #[\Override]
    public function getOffset(): int
    {
        return $this->offset;
    }
}
