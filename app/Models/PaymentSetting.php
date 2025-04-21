<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    /**
     * Récupère une valeur de paramètre par sa clé
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Définit ou met à jour une valeur de paramètre
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $description
     * @return PaymentSetting
     */
    public static function setValue($key, $value, $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description ?? ''
            ]
        );

        return $setting;
    }
}
