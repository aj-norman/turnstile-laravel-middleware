<?php

namespace Ajnorman\CfTurnstileLaravelMiddleware\View;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TurnstileComponent extends Component
{
    public function __construct(public ?string $action = null) { }

    public function render(): View
    {
        return view('components.turnstile');
    }
}
