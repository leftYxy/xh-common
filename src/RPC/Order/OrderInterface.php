<?php

declare(strict_types=1);

namespace YaoxyD\XhCommon\RPC\Order;

interface OrderInterface
{
    public const NAME = 'OrderOrderInterface';

    public function getInfo(): array;
}
