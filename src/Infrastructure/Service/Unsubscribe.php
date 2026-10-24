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
use App\Infrastructure\Contract\Service\UnsubscribeInterface;
use App\Infrastructure\Exception\ValidationException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Webify\Base\Application\Service\ConfigInterface;

/**
 * Unsubscribe service handles newsletter deletion requests.
 */
final readonly class Unsubscribe implements UnsubscribeInterface
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
	public function unsubscribe(array $data): array
	{
		$email = mb_trim($data['email'] ?? '', self::CHARACTERS_TO_BE_STRIPPED);

		if ('' === $email) {
			return ['statusCode' => 422, 'success' => false, 'message' => 'Valid email is required.'];
		}

		$request = $this->buildRequest($email, $this->config->get('mailList', []));

		try {
			$response = $this->httpClient
				->getClient([
					'timeout'     => 10,
					'http_errors' => false,
				])
				->sendRequest($request)
			;
		} catch (ClientExceptionInterface $exception) {
			$this->logger->error('Newsletter deletion connection failed', ['error' => $exception->getMessage()]);

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
	 * Build API request for the mailing list contact deletion.
	 *
	 * @param array<string, mixed> $mailListConfig
	 *
	 * @throws ValidationException
	 */
	private function buildRequest(string $email, array $mailListConfig): RequestInterface
	{
		if (
			[] === $mailListConfig
			|| (!isset($mailListConfig['url']) || '' === $mailListConfig['url'])
			|| (!isset($mailListConfig['apiKey']) || '' === $mailListConfig['apiKey'])
		) {
			throw ValidationException::forMissingConfig('Mail list configuration "key:mailList" is missing or empty.');
		}

		$url = rtrim((string) $mailListConfig['url'], '/');

		$encodedEmail = rawurlencode($email);

		return $this->psr17Factory
			->createRequest('DELETE', $url . '/contacts/' . $encodedEmail)
			->withHeader('Content-Type', 'application/json')
			->withHeader('api-key', (string) $mailListConfig['apiKey'])
			->withHeader('accept', 'application/json')
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
		// Success: 204 No Content is typical for DELETE, but 200 is also possible
		if (200 <= $statusCode && 300 > $statusCode) {
			return ['statusCode' => 200, 'success' => true, 'message' => 'Your data has been deleted. You will no longer receive updates.'];
		}

		// 404 means the contact was not found (already deleted or never existed)
		if (404 === $statusCode) {
			return ['statusCode' => 200, 'success' => true, 'message' => 'Your data has been deleted. You will no longer receive updates.'];
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

			if (!is_string($message)) {
				$message = 'Validation failed.';
			}

			return ['statusCode' => 400, 'success' => false, 'message' => $message];
		}

		$this->logger->error('Newsletter deletion failed', [
			'status'   => $statusCode,
			'response' => $responseData,
			'endpoint' => $endpoint,
		]);

		return [
			'statusCode' => 502,
			'success'    => false,
			'message'    => 'Deletion service is temporarily unavailable. Please try again later.',
		];
	}
}
