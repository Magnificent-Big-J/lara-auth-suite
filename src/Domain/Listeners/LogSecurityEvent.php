<?php

namespace Rainwaves\LaraAuthSuite\Domain\Listeners;

class LogSecurityEvent
{
    public function handle(object $event): void
    {
        logger()->debug('[authx] security event', ['event' => get_class($event), 'payload' => (array) $event]);
    }
}
