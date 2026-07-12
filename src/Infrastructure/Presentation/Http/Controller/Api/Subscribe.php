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

namespace App\Infrastructure\Presentation\Http\Controller\Api;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

use function DI\string;

/**
 * Subscribe controller handles newsletter signup form submissions.
 *
 * Forwards subscriber data to Listmonk via its public API.
 */
final readonly class Subscribe
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private Psr17Factory $psr17Factory,
	) {}

	/**
	 * Handles the subscription request.
	 *
	 * @param array<string, mixed> $args
	 */
	public function __invoke(ServerRequestInterface $request, array $args = []): ResponseInterface
	{
		$body = (string) $request->getBody();
		$data = json_decode($body, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			return $this->jsonResponse(400, ['success' => false, 'error' => 'Invalid request.']);
		}

		$name  = trim($data['name'] ?? '');
		$email = trim($data['email'] ?? '');

		if ('' === $name || '' === $email || false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return $this->jsonResponse(422, ['success' => false, 'error' => 'Valid name and email are required.']);
		}

		$listmonkUrl  = rtrim($_ENV['LISTMONK_API_URL'] ?? 'http://listmonk:9000', '/');
		$listmonkUser = $_ENV['LISTMONK_ADMIN_USER'] ?? 'admin';
		$listmonkPass = $_ENV['LISTMONK_ADMIN_PASSWORD'] ?? '';
		$ch           = curl_init($listmonkUrl . '/api/public/subscribers');

		curl_setopt_array(
			$ch,
			[
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT        => 10,
				CURLOPT_HTTPHEADER     => [
					'Content-Type: application/json',
				],
				CURLOPT_USERPWD        => $listmonkUser . ':' . $listmonkPass,
				CURLOPT_POSTFIELDS     => (string) json_encode(
					[
						'email'      => $email,
						'name'       => $name,
						'list_ids'   => [],
						'status'     => 'confirmed',
						'attributes' => [
							'source' => 'webifycms.com',
						],
					]
				),
			]
		);

		$response   = curl_exec($ch);
		$httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError  = curl_error($ch);

		curl_close($ch);

		if ('' !== $curlError) {
			return $this->jsonResponse(
				502,
				['success' => false, 'error' => 'Unable to connect to mailing list service.']
			);
		}

		if (409 === $httpCode) {
			return $this->jsonResponse(
				200,
				['success' => true, 'message' => 'You are already subscribed.']
			);
		}

		if (200 <= $httpCode && 300 > $httpCode) {
			return $this->jsonResponse(
				200,
				['success' => true, 'message' => 'Thank you for subscribing!']
			);
		}

		return $this->jsonResponse(
			502,
			['success' => false, 'error' => 'Subscription service is temporarily unavailable. Please try again later.']
		);
	}

	/**
	 * Returns a JSON response.
	 *
	 * @param array<string, mixed> $data
	 */
	private function jsonResponse(int $status, array $data): ResponseInterface
	{
		return $this->psr17Factory
			->createResponse($status)
			->withBody($this->psr17Factory->createStream((string) json_encode($data)))
			->withHeader('Content-Type', 'application/json')
		;
	}
}
