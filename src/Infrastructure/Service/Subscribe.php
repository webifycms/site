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
		$name  = trim($data['name'] ?? '');
		$email = trim($data['email'] ?? '');

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
		$responseData = json_decode($response->getBody()->getContents(), true);

		if (200 <= $statusCode && 300 > $statusCode) {
			return ['statusCode' => 200, 'success' => true, 'message' => 'Thank you for subscribing!'];
		}

		if (400 === $statusCode) {
			$errors = $responseData['errors'] ?? [];

			foreach ($errors as $error) {
				if ('has already been taken' === ($error['detail'] ?? '')) {
					return ['statusCode' => 409, 'success' => false, 'message' => 'This email is already subscribed.'];
				}
			}

			$message = $errors[0]['detail'] ?? 'Validation failed.';

			return ['statusCode' => 400, 'success' => false, 'message' => $message];
		}

		$this->logger->error('Newsletter subscription failed', [
			'status'   => $statusCode,
			'response' => $responseData,
			'endpoint' => (string) $request->getUri(),
		]);

		return [
			'statusCode' => $statusCode,
			'success'    => false,
			'message'    => 'Subscription service is temporarily unavailable. Please try again later.',
		];
	}

	/**
	 * Build API request for the mailing list.
	 *
	 * @param array<string, string> $mailListConfig
	 *
	 * @throws ValidationException
	 */
	private function buildRequest(string $email, string $name, array $mailListConfig): RequestInterface
	{
		if (
			[] === $mailListConfig
			|| !array_key_exists('url', $mailListConfig)
			|| !array_key_exists('apiKey', $mailListConfig)
		) {
			throw ValidationException::forMissingConfig('Mail list configuration "key:mailList" is missing or empty.');
		}

		return $this->psr17Factory
			->createRequest('POST', $mailListConfig['url'] . '/contacts')
			->withHeader('Content-Type', 'application/json')
			->withHeader(
				'Authorization',
				'Bearer ' . $mailListConfig['apiKey']
			)
			->withBody(
				$this->psr17Factory->createStream((string) json_encode([
					'data' => [
						'email'      => $email,
						'first_name' => $name,
						'status'     => 'active',
					],
				]))
			)
		;
	}
}
