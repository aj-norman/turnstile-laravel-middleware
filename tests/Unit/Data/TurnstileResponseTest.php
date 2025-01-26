<?php

use Ajnorman\CfTurnstileLaravelMiddleware\Data\TurnstileResponse;

$successfulPayload = new TurnstileResponse([
    'success' => true,
    'challenge_ts' => '2025-01-26T12:00:00Z',
    'hostname' => 'example.com',
    'action' => 'submit',
    'cdata' => 'cdata',
    'error-codes' => [],
    'metadata' => [
        'id' => '123456',
    ],
]);

$failedPayload = new TurnstileResponse([
    'success' => false,
    'challenge_ts' => '2025-01-26T12:00:00Z',
    'hostname' => 'example.com',
    'action' => 'submit',
    'cdata' => 'cdata',
    'error-codes' => ['missing-input-secret'],
    'metadata' => [
        'id' => '123456',
    ],
]);

describe('Testing `getSuccess`', function () use ($successfulPayload, $failedPayload) {
    it('should return true as success value')
        ->expect($successfulPayload->getSuccess())->toBeTrue();

    it('should return false as success value')
        ->expect($failedPayload->getSuccess())->toBeFalse();
});

describe('Testing `getChallengeTimestamp`', function () use ($successfulPayload) {
    it('should return a Carbon instance')
        ->expect($successfulPayload->getChallengeTimestamp())
        ->toBeInstanceOf(Carbon\Carbon::class);
});

describe('Testing `getHostname`', function () use ($successfulPayload) {
    it('should return the hostname')
        ->expect($successfulPayload->getHostname())->toBe('example.com');
});

describe('Testing `getAction`', function () use ($successfulPayload) {
    it('should return the action')
        ->expect($successfulPayload->getAction())->toBe('submit');
});

describe('Testing `getCData`', function () use ($successfulPayload) {
    it('should return the cdata')
        ->expect($successfulPayload->getCData())->toBe('cdata');
});

describe('Testing `getErrorCodes`', function () use ($successfulPayload, $failedPayload) {
    it('should return an empty array')
        ->expect($successfulPayload->getErrorCodes())->toBe([]);

    it('should return an array with error codes')
        ->expect($failedPayload->getErrorCodes())->toBe(['missing-input-secret']);
});

describe('Testing `getMetadata`', function () use ($successfulPayload) {
    it('should return the metadata')
        ->expect($successfulPayload->getMetadata())->toBe(['id' => '123456']);
});
