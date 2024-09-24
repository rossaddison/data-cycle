<?php

declare(strict_types=1);

namespace Yiisoft\Data\Cycle\Tests\Feature\Mysql\Reader\ReaderWithFilter;

use Yiisoft\Data\Cycle\Tests\Feature\Base\Reader\ReaderWithFilter\EqualsHandlerTestCase as BaseEqualsHandlerTest;

final class EqualsHandlerTestCase extends BaseEqualsHandlerTest
{
    public static $DRIVER = 'mysql';
}
