<?php

use Ajnorman\CfTurnstileLaravelMiddleware\Actions\ValidateTurnstileResponse;
use Ajnorman\CfTurnstileLaravelMiddleware\Data\TurnstileResponse;
use Ajnorman\CfTurnstileLaravelMiddleware\Exceptions\TurnstileConnectionException;
use Ajnorman\CfTurnstileLaravelMiddleware\Exceptions\TurnstileValidationException;
use Ajnorman\CfTurnstileLaravelMiddleware\Middleware\ValidateTurnstile;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

describe('Testing `handle` method', function () {
    test('`handle` should return a response', function () {
        $action = Mockery::mock(ValidateTurnstileResponse::class)
            ->shouldReceive('validate')
            ->once()
            ->andReturn(new TurnstileResponse(['success' => true]))
            ->getMock();

        $middleware = new ValidateTurnstile($action);

        $request = Request::create('http://localhost', 'POST', ['cf-turnstile-response' => 'test']);

        $response = $middleware->handle($request, function ($request) {
            return $request;
        });

        expect($response)->toBeInstanceOf(Request::class);
    });

    test('`handle` should return a response when `shouldValidateTurnstile` is `false`', function () {
        $action = Mockery::mock(ValidateTurnstileResponse::class)
            ->shouldNotReceive('validate')
            ->getMock();

        $middleware = new ValidateTurnstile($action);

        config(['cf-turnstile.enabled' => false]);

        $request = Request::create('http://localhost', 'POST', ['cf-turnstile-response' => 'test']);

        $response = $middleware->handle($request, function ($request) {
            return $request;
        });

        expect($response)->toBeInstanceOf(Request::class);
    });

    test('`handle` should return a response when `validate` throws `TurnstileConnectionException`', function () {
        $action = Mockery::mock(ValidateTurnstileResponse::class)
            ->shouldReceive('validate')
            ->once()
            ->andThrow(new TurnstileConnectionException('Failed to connect to the Cloudflare Turnstile API.'))
            ->getMock();

        $middleware = new ValidateTurnstile($action);

        $request = Request::create('http://localhost', 'POST', ['cf-turnstile-response' => 'test']);

        $response = $middleware->handle($request, function ($request) {
            return $request;
        });

        expect($response->getSession()->get('error'))->toEqual('Failed to connect to the Cloudflare Turnstile API.');
    });

    test('`handle` should return a response when `validate` throws `TurnstileValidationException`', function () {
        $action = Mockery::mock(ValidateTurnstileResponse::class)
            ->shouldReceive('validate')
            ->once()
            ->andThrow(new TurnstileValidationException('An unknown error occurred.'))
            ->getMock();

        $middleware = new ValidateTurnstile($action);

        $request = Request::create('http://localhost', 'POST', ['cf-turnstile-response' => 'test']);

        $response = $middleware->handle($request, function ($request) {
            return $request;
        });

        expect($response->getSession()->get('error'))->toEqual('An unknown error occurred.');
    });
});

describe('Testing `shouldValidateTurnstile` method', function () {
    test('`shouldValidateTurnstile` when `CF_TURNSTILE_ENABLED` is `true`', function () {
        $action = new ValidateTurnstileResponse(new Client());

        $middleware = new ValidateTurnstile($action);

        $response = $middleware->shouldValidateTurnstile();
        expect($response)->toBeTrue();
    });

    test('`shouldValidateTurnstile` when `CF_TURNSTILE_ENABLED` is `false`', function () {
        $action = new ValidateTurnstileResponse(new Client());

        $middleware = new ValidateTurnstile($action);

        config(['cf-turnstile.enabled' => false]);

        $response = $middleware->shouldValidateTurnstile();
        expect($response)->toBeFalse();
    });
});

describe('Testing `getChallengeResponse` method', function () {
    test('`getChallengeResponse` should return a value', function () {
        $action = new ValidateTurnstileResponse(new Client());

        $middleware = new ValidateTurnstile($action);

        $request = Request::create('http://localhost', 'POST', ['cf-turnstile-response' => 'test']);

        $response = $middleware->getChallengeResponse($request);
        expect($response)->toEqual('test');
    });
});
