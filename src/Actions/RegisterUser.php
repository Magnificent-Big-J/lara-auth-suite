<?php
namespace Rainwaves\LaraAuthSuite\Actions;

use Rainwaves\LaraAuthSuite\Contracts\RegistrationService;
use Illuminate\Contracts\Auth\Authenticatable;

readonly class RegisterUser
{
    public function __construct(private RegistrationService $service) {}
    /** @param array $payload validated register data */
    public function __invoke(array $payload): Authenticatable { return $this->service->register($payload); }
}
