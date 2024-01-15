<?php
/**
 * @CreateDate 2024-01-12 15:23
 */

declare(strict_types=1);

namespace YaoxyD\XhCommon\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\RpcMultiplex\Constant;
use Hyperf\Stringable\Str;
use Hyperf\Stringable\StrCache;
use Psr\Container\ContainerInterface;
use YaoxyD\XhCommon\RPC\Admin\AdminInterface;

use function Hyperf\Support\env;

#[Listener(99)]
class BootRPCConsumerListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        $interfaces = [
            AdminInterface::class => ['xh_admin', 9502],
        ];
        $consumers = [];
        foreach ($interfaces as $interface => [$host, $port]) {
            $consumers[] = $this->getConsumer($interface, $host, $port);
        }
        $this->container->get(ConfigInterface::class)->set('services.consumers', $consumers);
    }

    protected function getConsumer(string $interface, string $host, int $port): array
    {
        $key = Str::upper('RPC_' . StrCache::studly($host, '_'));
        if ($value = env($key)) {
            [$host, $port] = explode(':', $value);
        }
        return [
            'name' => $interface::NAME,
            'service' => $interface,
            'id' => $interface,
            'protocol' => Constant::PROTOCOL_DEFAULT,
            'load_balancer' => 'random',
            'nodes' => [
                ['host' => $host, 'port' => (int) $port],
            ],
            'options' => [
                'connect_timeout' => 5.0,
                'recv_timeout' => 5.0,
                'settings' => [
                    // 包体最大值，若小于 Server 返回的数据大小，则会抛出异常，故尽量控制包体大小
                    'package_max_length' => 1024 * 1024 * 2,
                ],
                // 重试次数，默认值为 2
                'retry_count' => 2,
                // 重试间隔，毫秒
                'retry_interval' => 100,
                // 多路复用客户端数量
                'client_count' => 4,
                // 心跳间隔 非 numeric 表示不开启心跳
                'heartbeat' => 30,
            ],
        ];
    }
}
