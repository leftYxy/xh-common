<?php

declare(strict_types=1);

namespace YaoxyD\XhCommon\RPC\Admin;

interface AdminInterface
{
    public const NAME = 'AdminAdminInterface';

    public function ping(): array;
}
