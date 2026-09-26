<?php

declare(strict_types=1);

namespace Agenciafmd\Postal\Notifications;

use Agenciafmd\Postal\Channels\EventChannel;
use Agenciafmd\Postal\Events\NotificationSent;
use Agenciafmd\Postal\Models\Postal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Symfony\Component\Mime\Email;

#[Backoff([10, 30, 60])]
#[Tries(4)]
final class SendNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array{greeting?: string|null, introLines?: array<int, string>, actionText?: string|null, actionUrl?: string|null, outroLines?: array<int, string>}  $data
     * @param  array<string, string>  $from  e-mail => nome, usado no reply-to
     * @param  array<int, string>  $attach  caminhos dos arquivos anexados
     */
    public function __construct(
        public array $data = [],
        public array $from = [],
        public array $attach = [],
        public ?string $subject = null,
    ) {}

    /**
     * @return array<int, class-string>
     */
    public function via(Postal $notifiable): array
    {
        return [
            MailChannel::class,
            EventChannel::class,
        ];
    }

    public function toMail(Postal $notifiable): MailMessage
    {
        $content = array_merge([
            'greeting' => $this->translate('Hi :name!', ['name' => $notifiable->to_name]),
            'introLines' => [
                $this->translate('This email sent by the website through the :name form.', ['name' => $notifiable->name]),
            ],
            'actionText' => null,
            'actionUrl' => null,
            'outroLines' => [
                //
            ],
        ], $this->data);

        $mail = (new MailMessage)
            ->markdown('filament-postal::markdown.email')
            ->theme('filament-postal::theme.tabler')
            ->level('default')
            ->subject($this->subject ?? config()->string('app.name') . ' | ' . $notifiable->subject);

        if ($content['greeting']) {
            $mail->greeting($content['greeting']);
        }

        foreach ($content['introLines'] as $introLine) {
            $mail->line($introLine);
        }

        if ($content['actionText'] && $content['actionUrl']) {
            $mail->action($content['actionText'], $content['actionUrl']);
        }

        foreach ($content['outroLines'] as $outroLine) {
            $mail->line($outroLine);
        }

        if ($this->from) {
            $mail->replyTo(key($this->from), current($this->from));
        }

        collect($notifiable->cc ?? [])
            ->filter(static fn (mixed $cc): bool => is_string($cc))
            ->each(static fn (string $cc): MailMessage => $mail->cc($cc));

        collect($notifiable->bcc ?? [])
            ->filter(static fn (mixed $bcc): bool => is_string($bcc))
            ->each(static fn (string $bcc): MailMessage => $mail->bcc($bcc));

        /* TODO: modificar para o attachFromStorage quando subir a versão do laravel */
        foreach ($this->attach as $attach) {
            $mail->attach($attach);
        }

        $mail->withSymfonyMessage(static function (Email $message): void {
            $message->getHeaders()
                ->addTextHeader(
                    'X-Mailgun-Tag', config()->string('app.name')
                );
        });

        return $mail;
    }

    /**
     * @param  array<string, string>  $data
     */
    public function toEvent(array $data): void
    {
        event(new NotificationSent($data));
    }

    /**
     * @param  array<string, string|null>  $replace
     */
    private function translate(string $key, array $replace = []): string
    {
        $translation = __($key, $replace);

        return is_string($translation) ? $translation : $key;
    }
}
