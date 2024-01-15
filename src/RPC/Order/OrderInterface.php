<?php

namespace YaoxyD\XhCommon\RPC\Order;

interface OrderInterface
{
	public const NAME = 'OrderOrderInterface';

	public function getInfo(): array;
}