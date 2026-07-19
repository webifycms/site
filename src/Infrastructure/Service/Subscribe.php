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

		if ('' === $name || '' === $email || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ['statusCode' => 422, 'success' => false, 'message' => 'Valid name and email are required.'];
		}

		$request = $this->buildRequest($name, $email, $this->config->get('mailList', []));

		try {
			$response = $this->httpClient
				->getClient([
					'timeout'     => 10,
					'http_errors' => false,
				])
				->sendRequest($request)
			;
		} catch (ClientExceptionInterface $exception) {
			$this->logger->error('Mailing list connection failed', ['error' => $exception->getMessage()]);

			return [
				'statusCode' => 502,
				'success'    => false,
				'message'    => 'Unable to connect to the mailing list service.',
			];
		}

		$statusCode = $response->getStatusCode();

		// Consume and close the response body to free memory
		$response->getBody()->getContents();

		if (409 === $statusCode) {
			return ['statusCode' => 200, 'success' => true, 'message' => 'You are already subscribed.'];
		}

		if (200 <= $statusCode && 300 > $statusCode) {
			return ['statusCode' => 200, 'success' => true, 'message' => 'Thank you for subscribing!'];
		}

		$this->logger->warning('Mailing list subscription failed', ['status' => $statusCode]);

		return [
			'statusCode' => 500,
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
			|| !array_key_exists('username', $mailListConfig)
			|| !array_key_exists('password', $mailListConfig)
		) {
			throw ValidationException::forMissingConfig('Mail list configuration "key:mailList" is missing or empty.');
		}

		return $this->psr17Factory
			->createRequest('POST', $mailListConfig['url'] . '/api/public/subscribers')
			->withHeader('Content-Type', 'application/json')
			->withHeader(
				'Authorization',
				'Basic ' . base64_encode($mailListConfig['username'] . ':' . $mailListConfig['password'])
			)
			->withBody(
				$this->psr17Factory->createStream((string) json_encode([
					'email'      => $email,
					'name'       => $name,
					'list_ids'   => [],
					'status'     => 'confirmed',
					'attributes' => [
						'source' => $this->config->get('id', 'webifycms'),
					],
				]))
			)
		;
	}
}
