<?php

namespace Ajnorman\CfTurnstileLaravelMiddleware\Actions;

use Ajnorman\CfTurnstileLaravelMiddleware\Data\TurnstileResponse;
use Ajnorman\CfTurnstileLaravelMiddleware\Exceptions\TurnstileConnectionException;
use Ajnorman\CfTurnstileLaravelMiddleware\Exceptions\TurnstileValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HttpException;

class ValidateTurnstileResponse
{
    public function __construct(private readonly Client $client) { }

    /**
     * @param  string  $challengeResponse
     * @param  string|null  $remoteIp
     *
     * @return TurnstileResponse
     * @throws GuzzleException
     * @throws TurnstileValidationException
     * @throws TurnstileConnectionException
     */
    public function validate(string $challengeResponse, string|null $remoteIp): TurnstileResponse
    {
        $response = $this->client->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'secret'   => config('cf-turnstile.secret_key'),
                'response' => $challengeResponse,
                'remoteip' => $remoteIp,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new TurnstileConnectionException('Failed to connect to the Cloudflare Turnstile API.');
        }

        $body = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);

        if (! $body['success']) {
            $error = match ($body['error-codes'][0] ?? null) {
                'missing-input-secret'   => 'The secret parameter is missing.',
                'invalid-input-secret'   =>	'The secret parameter was invalid, did not exist, or is a testing secret key with a non-testing response.',
                'missing-input-response' =>	'The response parameter (token) was not passed.',
                'invalid-input-response' =>	'The response parameter (token) is invalid or has expired. Most of the time, this means a fake token has been used. If the error persists, contact customer support.',
                'bad-request'            =>	'The request was rejected because it was malformed.',
                'timeout-or-duplicate'   =>	'The response parameter (token) has already been validated before. This means that the token was issued five minutes ago and is no longer valid, or it was already redeemed.',
                'internal-error'         => 'The reCAPTCHA verification failed due to an internal error. Please try again later.',
                default                  => 'An unknown error occurred.',
            };

            throw new TurnstileValidationException($error);
        }

        return new TurnstileResponse($body);
    }
}
