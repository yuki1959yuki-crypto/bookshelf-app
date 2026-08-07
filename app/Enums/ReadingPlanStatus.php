<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case NotStarted = '未着手';
    case Reading = '読書中';
    case Completed = '読了';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NotStarted => 'bg-gray-100 text-gray-800',
            self::Reading => 'bg-blue-100 text-blue-800',
            self::Completed => 'bg-green-100 text-green-800',
        };
    }
}
