@once
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endonce

<div class="cf-turnstile"
     data-sitekey="{{ config('cf-turnstile.site_key') }}"
     data-action="{{ $action }}">
</div>
