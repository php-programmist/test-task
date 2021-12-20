<?php

namespace App\Model\GuzzleClientFactory;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\StreamInterface;

class GuzzleClientTestFactory implements GuzzleClientFactoryInterface
{
    public static array $responses = [];

    /**
     * @var bool - Установить в true, если в процессе теста будет создаваться несколько клиентов. При создании нового клиента первый элемент из массива $responses будет из него удаляться и передаваться в ново созданный клиент
     */
    public static bool $shiftResponses = false;
    public static array $transactions = [];

    public function create(array $config = []): Client
    {
        $history = Middleware::history(self::$transactions);
        $mock = new MockHandler($this->getMockedResponses());
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $config = array_merge($config, ['handler' => $handlerStack]);

        return new Client($config);
    }

    public static function shiftRequest(): Request
    {
        $transaction = array_shift(self::$transactions);

        return $transaction['request'];
    }

    public static function clearTransactions(): void
    {
        self::$transactions = [];
    }

    /**
     * @param int $status Код ответа
     * @param array<string, string|string[]> $headers Заголовки ответа. Если не указан заголовок Content-Type, то будет установлен со значением application/json
     * @param string|StreamInterface|null $body Тело ответа
     */
    public static function addResponse(
        int $status = 200,
        array $headers = [],
        StreamInterface|string $body = null,
    ): void {
        $headers['Content-Type'] ??= 'application/json';
        self::$responses[] = new Response($status, $headers, $body);
    }

    /**
     * @return array
     */
    private function getMockedResponses(): array
    {
        if (self::$shiftResponses) {
            $responses = array_shift(self::$responses);
            if (!is_array($responses)) {
                $responses = [$responses];
            }
        } else {
            $responses = self::$responses;
        }

        return $responses;
    }
}