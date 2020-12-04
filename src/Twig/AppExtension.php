<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('highlight', [$this, 'highlight']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    public function highlight($subject, $word)
    {
        $subject = str_replace(
            [
                $word,
                mb_strtoupper($word),
                ucfirst($word),
                mb_strtolower($word),
            ],
            [
                '<span class="highlight">'.$word.'</span>',
                '<span class="highlight">'.mb_strtoupper($word).'</span>',
                '<span class="highlight">'.ucfirst($word).'</span>',
                '<span class="highlight">'.mb_strtolower($word).'</span>',
            ],
            $subject
        );
        return $subject;
    }
}
