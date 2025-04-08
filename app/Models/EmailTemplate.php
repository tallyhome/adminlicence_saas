<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class EmailTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'html_content',
        'text_content',
        'description',
        'variables',
        'is_active',
        'is_system'
    ];

    protected $casts = [
        'variables' => 'array',
        'subject' => 'array',
        'content' => 'array',
        'is_active' => 'boolean'
    ];

    public function getVariablesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setVariablesAttribute($value)
    {
        $this->attributes['variables'] = json_encode($value);
    }

    /**
     * Remplacer les variables dans le contenu du template
     *
     * @param array $data
     * @return string
     */
    public function replaceVariables(array $data): string
    {
        $content = $this->content;
        $subject = $this->subject;

        foreach ($data as $key => $value) {
            $placeholder = '{' . $key . '}';
            $content = str_replace($placeholder, $value, $content);
            $subject = str_replace($placeholder, $value, $subject);
        }

        $this->attributes['content'] = $content;
        $this->attributes['subject'] = $subject;

        return $content;
    }

    /**
     * Valider les variables requises
     *
     * @param array $data
     * @return bool
     */
    public function validateVariables(array $data): bool
    {
        $requiredVariables = collect($this->variables)->pluck('name')->map(function ($name) {
            return trim($name, '{}');
        });

        $providedVariables = collect($data)->keys();

        return $requiredVariables->diff($providedVariables)->isEmpty();
    }
}