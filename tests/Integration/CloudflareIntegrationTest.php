<?php

use Ajnorman\CfTurnstileLaravelMiddleware\Actions\ValidateTurnstileResponse;
use Ajnorman\CfTurnstileLaravelMiddleware\Data\TurnstileResponse;
use Ajnorman\CfTurnstileLaravelMiddleware\Exceptions\TurnstileValidationException;
use GuzzleHttp\Client;

it('returns a  `TurnstileResponse`  object on a successful request', function () {
    config([
        'cf-turnstile.secret_key' => '1x0000000000000000000000000000000AA',
    ]);

    $action = new ValidateTurnstileResponse(new Client());
    $response = $action->validate('1x00000000000000000000AA', null);

    expect($response)->toBeInstanceOf(TurnstileResponse::class)
        ->getSuccess()->toBeTrue();
});

it('throws a  `TurnstileValidationException`  on an invalid `secret_key`', function () {
    config([
        'cf-turnstile.secret_key' => '2x0000000000000000000000000000000AA',
    ]);

    $action = new ValidateTurnstileResponse(new Client());
    expect(fn () => $action->validate('1x00000000000000000000AA', null))
        ->toThrow(TurnstileValidationException::class, 'The response parameter (token) is invalid or has expired. Most of the time, this means a fake token has been used. If the error persists, contact customer support.');
});


