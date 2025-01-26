<?php

use Ajnorman\CfTurnstileLaravelMiddleware\Actions\ValidateTurnstileResponse;
use Ajnorman\CfTurnstileLaravelMiddleware\Data\TurnstileResponse;
use Ajnorman\CfTurnstileLaravelMiddleware\Exceptions\{TurnstileConnectionException, TurnstileValidationException};
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

describe('Testing a successful flow', function () {
    it('returns a successful response when the request is valid', function () {
        // only testing success, so we don't need to mock anything else
        $payload = [
            'success' => true,
        ];

        $mockHandler = new MockHandler([
            new Response(status: 200, body: json_encode($payload)),
        ]);
        $handlerStack = new HandlerStack($mockHandler);

        $response = new ValidateTurnstileResponse(new Client(['handler' => $handlerStack]));
        $result = $response->validate('fake-response', null);

        expect($result)->toBeInstanceOf(TurnstileResponse::class)
            ->getSuccess()->toBeTrue();
    });
});

describe('Testing a connection failed flow', function () {
    it('throws an exception when the request is invalid', function () {
        $mockHandler = new MockHandler([
            new Response(status: 502, body: ''),
        ]);
        $handlerStack = new HandlerStack($mockHandler);

        $response = new ValidateTurnstileResponse(new Client(['handler' => $handlerStack]));

        $closure = fn() => $response->validate('fake-response', null);
        expect($closure)->toThrow(TurnstileConnectionException::class, 'Failed to connect to the Cloudflare Turnstile API.');
    });
});

describe('Testing validation failed flow', function () {
    it('throws an exception when the request is invalid', function (string $errorCode, string $expectedErrorMessage) {
        $payload = [
            'success' => false,
            'error-codes' => [$errorCode],
        ];

        $mockHandler = new MockHandler([
            new Response(status: 200, body: json_encode($payload)),
        ]);
        $handlerStack = new HandlerStack($mockHandler);
        $action = new ValidateTurnstileResponse(new Client(['handler' => $handlerStack]));

        $closure = fn() => $action->validate('fake-response', null);
        expect($closure)->toThrow(TurnstileValidationException::class, $expectedErrorMessage);
    })
    ->with([
        'missing-input-secret' => [
            'errorCode' => 'missing-input-secret',
            'expectedErrorMessage' => 'The secret parameter is missing.'
        ],
        'invalid-input-secret' => [
            'errorCode' => 'invalid-input-secret',
            'expectedErrorMessage' => 'The secret parameter was invalid, did not exist, or is a testing secret key with a non-testing response.'
        ],
        'missing-input-response' => [
            'errorCode' => 'missing-input-response',
            'expectedErrorMessage' => 'The response parameter (token) was not passed.'
        ],
        'invalid-input-response' => [
            'errorCode' => 'invalid-input-response',
            'expectedErrorMessage' => 'The response parameter (token) is invalid or has expired. Most of the time, this means a fake token has been used. If the error persists, contact customer support.'
        ],
        'bad-request' => [
            'errorCode' => 'bad-request',
            'expectedErrorMessage' => 'The request was rejected because it was malformed.'
        ],
        'timeout-or-duplicate' => [
            'errorCode' => 'timeout-or-duplicate',
            'expectedErrorMessage' => 'The response parameter (token) has already been validated before. This means that the token was issued five minutes ago and is no longer valid, or it was already redeemed.'
        ],
        'internal-error' => [
            'errorCode' => 'internal-error',
            'expectedErrorMessage' => 'The reCAPTCHA verification failed due to an internal error. Please try again later.'
        ],
        'not-a-real-error' => [
            'errorCode' => 'not-a-real-error',
            'expectedErrorMessage' => 'An unknown error occurred.'
        ],
    ]);
});
