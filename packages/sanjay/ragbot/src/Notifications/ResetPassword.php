<?php

namespace Sanjay\Ragbot\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Sanjay\Ragbot\Models\Project;

class ResetPassword extends Notification
{
    use Queueable;

    public function __construct(public string $token, protected Project $project) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your account in '.$this->project->name.'.')
            ->action('Reset Password', route('ragbot.password.reset', [
                'project_slug' => $this->project->slug,
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]))
            ->line('This password reset link will expire in '.config('auth.passwords.users.expire').' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
