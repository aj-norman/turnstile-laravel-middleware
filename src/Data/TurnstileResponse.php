<?php

namespace Ajnorman\CfTurnstileLaravelMiddleware\Data;

use Carbon\Carbon;

readonly class TurnstileResponse
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(private array $payload) { }

    public function getSuccess(): bool
    {
        return $this->payload['success'];
    }

    public function getChallengeTimestamp(): Carbon
    {
        return Carbon::createFromTimestamp($this->payload['challenge_ts']);
    }

    public function getHostname(): string
    {
        return $this->payload['hostname'];
    }

    public function getAction(): string
    {
        return $this->payload['action'];
    }

    public function getCData(): string
    {
        return $this->payload['cdata'];
    }

    /**
     * @return array<int, string>
     */
    public function getErrorCodes(): array
    {
        return $this->payload['error-codes'];
    }

    public function getMetadata(): array
    {
        return $this->payload['metadata'];
    }
}
