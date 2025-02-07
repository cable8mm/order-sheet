<?php

namespace Cable8mm\OrderSheet\Enums;

enum OrderSheetType
{
    case PlayautoType;

    public function factoryClass(): string
    {
        return match ($this) {
            self::PlayautoType => \Cable8mm\OrderSheet\Factories\PlayautoFactory::class,
        };
    }
}
