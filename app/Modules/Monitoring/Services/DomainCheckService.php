<?php

namespace App\Modules\Monitoring\Services;

use App\Modules\Domain\Models\Domain;
use App\Modules\Monitoring\DTOs\CheckResult;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\TransferStats;

class DomainCheckService
{
    public function check(Domain $domain): CheckResult
    {
        $transferTime = null;

        $client = new Client([
            'timeout'         => $domain->request_timeout,
            'connect_timeout' => min(5, $domain->request_timeout),
            'verify'          => false,
            'allow_redirects' => ['max' => 5, 'strict' => false],
            'http_errors'     => false,
            'on_stats'        => function (TransferStats $stats) use (&$transferTime) {
                $transferTime = (int) ($stats->getTransferTime() * 1000);
            },
        ]);

        try {
            $response = $client->request($domain->check_method, $domain->url);
            $httpCode = $response->getStatusCode();

            // 2xx и 3xx считаем доступными; 4xx/5xx — недоступными
            if ($httpCode >= 200 && $httpCode < 400) {
                return CheckResult::success($httpCode, $transferTime ?? 0);
            }

            return CheckResult::failure($httpCode, "HTTP {$httpCode}");

        } catch (ConnectException $e) {
            return CheckResult::failure(null, 'Ошибка подключения: ' . $this->sanitizeMessage($e->getMessage()));
        } catch (RequestException $e) {
            $code    = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            $message = $e->hasResponse()
                ? "HTTP {$code}"
                : 'Ошибка запроса: ' . $this->sanitizeMessage($e->getMessage());

            return CheckResult::failure($code, $message);
        } catch (\Throwable $e) {
            return CheckResult::failure(null, 'Неизвестная ошибка: ' . $this->sanitizeMessage($e->getMessage()));
        }
    }

    /** Обрезаем длинные сообщения Guzzle перед сохранением в БД. */
    private function sanitizeMessage(string $message): string
    {
        return mb_substr($message, 0, 500);
    }
}
