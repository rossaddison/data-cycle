<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Reader\FilterHandler\LikeHandler;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\LikeMode;
use Yiisoft\Data\Reader\Iterable\Context;
use Yiisoft\Data\Reader\Iterable\IterableFilterHandlerInterface;

abstract class BaseLikeHandler implements IterableFilterHandlerInterface
{
    protected array $escapingReplacements = [
        '%' => '\%',
        '_' => '\_',
        '\\' => '\\\\',
    ];

    #[\Override]
    public function getFilterClass(): string
    {
        return Like::class;
    }

    /**
     * Prepare the SQL LIKE pattern according to LikeMode.
     * Accepts LikeMode as a parameter, defaulting to Contains for backward compatibility.
     */
    protected function prepareValue(string|\Stringable $value, LikeMode $mode = LikeMode::Contains): string
    {
        $stringValue = (string) $value;
        $escapedValue = strtr($stringValue, $this->escapingReplacements);

        return match ($mode) {
            LikeMode::Contains => '%' . $escapedValue . '%',
            LikeMode::StartsWith => $escapedValue . '%',
            LikeMode::EndsWith => '%' . $escapedValue,
        };
    }
    
    #[\Override]
    public function match(object|array $item, FilterInterface $filter, Context $context): bool
    {
        /** @var Like $filter */

        $itemValue = $context->readValue($item, $filter->field);
        if (!is_string($itemValue)) {
            return false;
        }

        $searchValue = $filter->value;
        if ($searchValue === '') {
            return true;
        }
        
        /**
         * @var string $searchValue
         */
        return match ($filter->mode) {
            LikeMode::Contains => $this->matchContains($itemValue, $searchValue, $filter->caseSensitive),
            LikeMode::StartsWith => $this->matchStartsWith($itemValue, $searchValue, $filter->caseSensitive),
            LikeMode::EndsWith => $this->matchEndsWith($itemValue, $searchValue, $filter->caseSensitive),
        };
    }

    private function matchContains(string $value, string $search, ?bool $caseSensitive): bool
    {
        return $caseSensitive === true
            ? str_contains($value, $search)
            : mb_stripos($value, $search) !== false;
    }

    private function matchStartsWith(string $value, string $search, ?bool $caseSensitive): bool
    {
        return $caseSensitive === true
            ? str_starts_with($value, $search)
            : mb_stripos($value, $search) === 0;
    }

    private function matchEndsWith(string $value, string $search, ?bool $caseSensitive): bool
    {
        if ($caseSensitive === true) {
            return str_ends_with($value, $search);
        }

        return mb_strtolower(mb_substr($value, -mb_strlen($search))) === mb_strtolower($search);
    }
}