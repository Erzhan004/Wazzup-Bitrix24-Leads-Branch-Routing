<?php

declare(strict_types=1);

namespace App\Wazzup;

use App\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class WazzupClient
{
  private Client $http;
  private string $apiKey;

  public function __construct(
    private readonly Config $config,
    private readonly LoggerInterface $logger,
  ) {
    $this->apiKey = (string) $config->require('wazzup.api_key');

    $this->http = new Client([
      'base_uri' => 'https://api.wazzup24.com/v3/',
      'connect_timeout' => (float) $config->get('wazzup.http.connect_timeout', 5.0),
      'timeout' => (float) $config->get('wazzup.http.timeout', 20.0),
      'http_errors' => false,
      'headers' => [
        'Authorization' => 'Bearer ' . $this->apiKey,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
      ],
    ]);
  }

  public function sendText(string $channelId, string $chatType, string $chatId, string $text): string
  {
    $channelId = trim($channelId);
    $chatType = trim($chatType) !== '' ? trim($chatType) : 'whatsapp';
    $chatId = preg_replace('/\D+/', '', $chatId) ?? '';
    $text = trim($text);

    if ($channelId === '' || $chatId === '' || $text === '') {
      throw new RuntimeException('Wazzup sendText requires channelId, chatId and text.');
    }

    try {
      $response = $this->http->post('message', [
        'json' => [
          'channelId' => $channelId,
          'chatType' => $chatType,
          'chatId' => $chatId,
          'text' => $text,
        ],
      ]);
    } catch (GuzzleException $e) {
      $this->logger->error('WAZZUP_SEND_TRANSPORT_ERROR', [
        'message' => $e->getMessage(),
        'chatId' => $chatId,
      ]);

      throw new RuntimeException('Wazzup transport error: ' . $e->getMessage(), 0, $e);
    }

    $status = $response->getStatusCode();
    $body = (string) $response->getBody();
    $decoded = json_decode($body, true);

    if ($status < 200 || $status >= 300) {
      $this->logger->error('WAZZUP_SEND_FAILED', [
        'httpStatus' => $status,
        'body' => $body,
        'chatId' => $chatId,
        'channelId' => $channelId,
      ]);

      $error = is_array($decoded)
        ? (string) ($decoded['error'] ?? $decoded['description'] ?? $body)
        : $body;

      throw new RuntimeException('Wazzup send failed: ' . $error);
    }

    $messageId = is_array($decoded) ? (string) ($decoded['messageId'] ?? '') : '';

    $this->logger->info('WAZZUP_BRANCH_PHONE_SENT', [
      'messageId' => $messageId,
      'chatId' => $chatId,
      'channelId' => $channelId,
      'chatType' => $chatType,
    ]);

    return $messageId;
  }
}
