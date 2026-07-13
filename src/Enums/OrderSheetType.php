<?php

namespace Cable8mm\OrderSheet\Enums;

use Cable8mm\OrderSheet\Factories\PlayautoFactory;

enum OrderSheetType
{
    case PlayautoType;

    public function factoryClass(): string
    {
        return match ($this) {
            self::PlayautoType => PlayautoFactory::class,
        };
    }
}
