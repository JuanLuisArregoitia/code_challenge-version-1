<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token): string {
            $frontendUrl = rtrim((string) config('app.frontend_url'), '/');

            return $frontendUrl.'/reset-password?'.http_build_query([
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Please confirm your email address to unlock the protected areas of the application.')
                ->action('Verify Email Address', $this->verificationFrontendUrl($url))
                ->line('If you did not create an account, no further action is required.');
        });
    }

    private function verificationFrontendUrl(string $url): string
    {
        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');
        $parts = parse_url($url);
        $segments = explode('/', trim($parts['path'] ?? '', '/'));
        $id = $segments[count($segments) - 2] ?? null;
        $hash = $segments[count($segments) - 1] ?? null;

        parse_str($parts['query'] ?? '', $query);

        return $frontendUrl.'/verify-email?'.http_build_query(array_filter([
            'id' => $id,
            'hash' => $hash,
            'expires' => $query['expires'] ?? null,
            'signature' => $query['signature'] ?? null,
        ], static fn ($value) => $value !== null && $value !== ''));
    }
}
