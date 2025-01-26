<?php

namespace Ajnorman\CfTurnstileLaravelMiddleware\Middleware;

use Ajnorman\CfTurnstileLaravelMiddleware\Actions\ValidateTurnstileResponse;
use Ajnorman\CfTurnstileLaravelMiddleware\Exceptions\TurnstileConnectionException;
use Ajnorman\CfTurnstileLaravelMiddleware\Exceptions\TurnstileValidationException;
use Closure;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTurnstile
{
    public function __construct(private ValidateTurnstileResponse $validateTurnstile) { }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $this->shouldValidateTurnstile()) {
            return $next($request);
        }

        try {
            $this->validateTurnstile->validate(
                $this->getChallengeResponse($request),
                null,
            );
        } catch (TurnstileConnectionException|GuzzleException $e) {
            return back()->with('error', 'Failed to connect to the Cloudflare Turnstile API.');
        } catch (TurnstileValidationException $e) {
            return back()->with('error', $e->getMessage());
        }

        return $next($request);
    }

    /**
     * Determine if the Turnstile should be validated.
     *
     * @return bool
     */
    public function shouldValidateTurnstile(): bool
    {
        return config('cf-turnstile.enabled');
    }

    /**
     * Get the challenge response from the request.
     *
     * @param  Request  $request
     * @return string
     */
    final public function getChallengeResponse(Request $request): string
    {
        return $request->input('cf-turnstile-response');
    }
}
