<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Tests\Feature\Pgsql\Reader\ReaderWithFilter;

use Yiisoft\Data\Cycle\Tests\Feature\Base\Reader\ReaderWithFilter\GreaterThanHandlerTestCase as
BaseGreaterThanHandlerTest;

final class GreaterThanHandlerTestCase extends BaseGreaterThanHandlerTest
{
    public static $DRIVER = 'pgsql';
}
