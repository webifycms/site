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

namespace App\Infrastructure\Presentation\Api\Model;

use App\Infrastructure\Exception\ValidationException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Model for subscribe request.
 */
final readonly class Subscribe
{
	/**
	 * The constructor.
	 */
	public function __construct(
		#[Assert\NotBlank(message: 'Valid name is required.')]
		#[Assert\Length(min: 2, max: 100)]
		public string $name,
		#[Assert\NotBlank(message: 'Valid email is required.')]
		#[Assert\Email(message: 'Valid email is required.')]
		public string $email
	) {}

	/**
	 * Create model from array and validate.
	 *
	 * @param array<string, mixed> $data
	 *
	 * @throws ValidationException
	 */
	public static function createFromArray(array $data, ValidatorInterface $validator): self
	{
		$name       = trim((string) ($data['name'] ?? ''));
		$email      = trim((string) ($data['email'] ?? ''));
		$model      = new self($name, $email);
		$violations = $validator->validate($model);

		if (count($violations) > 0) {
			foreach ($violations as $violation) {
				throw ValidationException::forRequestValidationFailed(
					(string) $violation->getMessage(),
					$violation->getPropertyPath()
				);
			}
		}

		return $model;
	}

	/**
	 * Convert to array for service.
	 *
	 * @return array{name: string, email: string}
	 */
	public function toArray(): array
	{
		return [
			'name'  => $this->name,
			'email' => $this->email,
		];
	}
}
