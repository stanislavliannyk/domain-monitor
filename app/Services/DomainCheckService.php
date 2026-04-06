<?php

namespace App\Services;

use App\DTOs\CheckResult;
use App\Models\Domain;
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

            // Treat 2xx and 3xx as up; 4xx/5xx as down
            if ($httpCode >= 200 && $httpCode < 400) {
                return CheckResult::success($httpCode, $transferTime ?? 0);
            }

            return CheckResult::failure($httpCode, "HTTP {$httpCode} response");

        } catch (ConnectException $e) {
            return CheckResult::failure(null, 'Connection failed: ' . $this->sanitizeMessage($e->getMessage()));
        } catch (RequestException $e) {
            $code    = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            $message = $e->hasResponse()
                ? "HTTP {$code} response"
                : 'Request failed: ' . $this->sanitizeMessage($e->getMessage());

            return CheckResult::failure($code, $message);
        } catch (\Throwable $e) {
            return CheckResult::failure(null, 'Unexpected error: ' . $this->sanitizeMessage($e->getMessage()));
        }
    }

    private function sanitizeMessage(string $message): string
    {
        // Trim potentially huge Guzzle stack traces from stored messages
        return mb_substr($message, 0, 500);
    }
}
