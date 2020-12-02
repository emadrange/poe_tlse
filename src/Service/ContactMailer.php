<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ContactMailer
{
    /**
     * @var string
     */
    private $receiver;

    /**
     * @var string
     */
    private $title;

    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(string $webmasterMail, string $poeTitle, MailerInterface $mailer)
    {
        $this->receiver = $webmasterMail;
        $this->title = $poeTitle;
        $this->mailer = $mailer;
    }

    public function sendMail(array $data)
    {
        $mail = (new TemplatedEmail())
            ->from($data['email'])
            ->to($this->receiver)
            ->subject($data['subject'])
            ->htmlTemplate('default/contact_mail.html.twig')
            ->context([
                'data' => $data,
                'day_date' => new \DateTime(),
                'title' => $this->title,
            ]);

        $this->mailer->send($mail);
    }
}