<?php

/**
 * The file is part of the "webifycms/site", WebifyCMS site.
 *
 * @see https://webifycms.com
 *
 * @copyright Copyright (c) 2026 WebifyCMS
 * @license https://webifycms.com/license
 * @author Mohammed Shifreen <mshifreen@gmail.com>
 */
declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Infrastructure\Contract\HttpClientInterface;
use App\Infrastructure\Contract\Service\SubscribeInterface;
use App\Infrastructure\Exception\ValidationException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Webify\Base\Application\Service\ConfigInterface;

/**
 * Subscribe service handles newsletter subscription logic.
 */
final readonly class Subscribe implements SubscribeInterface
{
	/**
	 * The characters to be stripped out.
	 */
	private const CHARACTERS_TO_BE_STRIPPED = " \t\n\r\0\x0B\xC2\xA0";

	/**
	 * The constructor.
	 */
	public function __construct(
		private HttpClientInterface $httpClient,
		private Psr17Factory $psr17Factory,
		private ConfigInterface $config,
		private LoggerInterface $logger,
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(array $data): array
	{
		$name  = mb_trim($data['name'] ?? '', self::CHARACTERS_TO_BE_STRIPPED);
		$email = mb_trim($data['email'] ?? '', self::CHARACTERS_TO_BE_STRIPPED);

		if ('' === $name || '' === $email) {
			return ['statusCode' => 422, 'success' => false, 'message' => 'Valid name and email are required.'];
		}

		$request = $this->buildRequest($email, $name, $this->config->get('mailList', []));

		try {
			$response = $this->httpClient
				->getClient([
					'timeout'     => 10,
					'http_errors' => false,
				])
				->sendRequest($request)
			;
		} catch (ClientExceptionInterface $exception) {
			$this->logger->error('Newsletter connection failed', ['error' => $exception->getMessage()]);

			return [
				'statusCode' => 502,
				'success'    => false,
				'message'    => 'Unable to connect to the newsletter service.',
			];
		}

		$statusCode   = $response->getStatusCode();
		$responseBody = (string) $response->getBody();
		$responseData = json_decode($responseBody, true);

		return $this->handleResponse(
			$statusCode,
			is_array($responseData) ? $responseData : [],
			(string) $request->getUri()
		);
	}

	/**
	 * Build API request for the mailing list.
	 *
	 * @param array<string, mixed> $mailListConfig
	 *
	 * @throws ValidationException
	 */
	private function buildRequest(string $email, string $name, array $mailListConfig): RequestInterface
	{
		if (
			[] === $mailListConfig
			|| (!isset($mailListConfig['url']) || '' === $mailListConfig['url'])
			|| (!isset($mailListConfig['apiKey']) || '' === $mailListConfig['apiKey'])
		) {
			throw ValidationException::forMissingConfig('Mail list configuration "key:mailList" is missing or empty.');
		}

		$url           = rtrim((string) $mailListConfig['url'], '/');
		$listIds       = array_values(
			array_filter(
				(array) ($mailListConfig['listIds'] ?? []),
				static fn ($id) => is_int($id) && 0 < $id
			)
		);
		$updateEnabled = filter_var($mailListConfig['updateEnabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
		$payload       = [
			'email'         => $email,
			'attributes'    => [
				'FNAME' => $name,
			],
			'updateEnabled' => $updateEnabled,
		];

		if ([] !== $listIds) {
			$payload['listIds'] = $listIds;
		}

		return $this->psr17Factory
			->createRequest('POST', $url . '/contacts')
			->withHeader('Content-Type', 'application/json')
			->withHeader('api-key', (string) $mailListConfig['apiKey'])
			->withHeader('accept', 'application/json')
			->withBody(
				$this->psr17Factory->createStream((string) json_encode($payload))
			)
		;
	}

	/**
	 * Handle API response.
	 *
	 * @param array<string, mixed> $responseData
	 *
	 * @return array{statusCode: int, success: bool, message: string}
	 */
	private function handleResponse(int $statusCode, array $responseData, string $endpoint): array
	{
		if (200 <= $statusCode && 300 > $statusCode) {
			return ['statusCode' => 200, 'success' => true, 'message' => 'Thank you for subscribing!'];
		}

		if (409 === $statusCode) {
			return ['statusCode' => 409, 'success' => false, 'message' => 'This email is already subscribed.'];
		}

		if (400 === $statusCode) {
			$message = 'Validation failed.';

			if ([] !== $responseData) {
				$firstError = $responseData['errors'][0] ?? null;
				if (is_array($firstError)) {
					$message = $firstError['detail'] ?? $firstError['message'] ?? $message;
				}

				if (is_string($responseData['message'] ?? null)) {
					$message = $responseData['message'];
				}
			}

			if (is_string($message)) {
				$lowerMessage = strtolower($message);
				if (
					strpos($lowerMessage, 'already exists') !== false
					|| strpos($lowerMessage, 'already exist') !== false
					|| strpos($lowerMessage, 'duplicate') !== false
				) {
					return ['statusCode' => 409, 'success' => false, 'message' => 'This email is already subscribed.'];
				}
			}

			if (!is_string($message)) {
				$message = 'Validation failed.';
			}

			return ['statusCode' => 400, 'success' => false, 'message' => $message];
		}

		$this->logger->error('Newsletter subscription failed', [
			'status'   => $statusCode,
			'response' => $responseData,
			'endpoint' => $endpoint,
		]);

		return [
			'statusCode' => 502,
			'success'    => false,
			'message'    => 'Subscription service is temporarily unavailable. Please try again later.',
		];
	}
}
