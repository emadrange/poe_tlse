<?php

namespace App\Service;

use Symfony\Contracts\Translation\TranslatorInterface;

class MessageGenerator
{
    private $translator;

    private $title;

    public function __construct(TranslatorInterface $translator, string $poeTitle)
    {
        $this->translator = $translator;
        $this->title = $poeTitle;
    }

    public function getMessage(): string
    {
        $messages = [
            'message_1',
            'message_2',
            'message_3',
        ];

        return $this->translator->trans(
            'home.'.$messages[array_rand($messages)], [
                '%poe%' => $this->title,
        ]);
    }
}