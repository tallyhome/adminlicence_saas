<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailConfig extends Model
{
    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'template_name',
        'template_content'
    ];

    protected $casts = [
        'port' => 'integer',
    ];

    /**
     * Get the mail configuration as an array.
     *
     * @return array
     */
    public function toMailConfig(): array
    {
        return [
            'transport' => $this->mailer,
            'host' => $this->host,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'encryption' => $this->encryption,
            'from' => [
                'address' => $this->from_address,
                'name' => $this->from_name,
            ],
        ];
    }
}