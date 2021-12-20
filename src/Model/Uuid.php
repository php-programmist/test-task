<?php


namespace App\Model;


class Uuid
{
    /**
     * @return string
     */
    public static function create(): string
    {
        $guidChars = md5(uniqid(mt_rand(), true));
        return self::format($guidChars);
    }

    /**
     * Форматирование строки UUID
     * @param $guidChars
     * @return string
     */
    public static function format($guidChars): string
    {
        return implode('-', [
            substr($guidChars, 0, 8),
            substr($guidChars, 8, 4),
            substr($guidChars, 12, 4),
            substr($guidChars, 16, 4),
            substr($guidChars, 20, 12),
        ]);
    }

    public static function isValidUuid(string $uuid): bool
    {
        return 1 === preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $uuid);
    }
}