<?php

namespace App\Services\Mail;

use App\Models\MailConfig;

class MailTemplateService
{
    /**
     * Récupérer un template d'email
     *
     * @param string $templateName
     * @return array|null
     */
    public function getTemplate(string $templateName): ?array
    {
        $template = MailConfig::where('template_name', $templateName)->first();
        
        if (!$template) {
            return null;
        }

        return [
            'name' => $template->template_name,
            'content' => $template->template_content
        ];
    }

    /**
     * Sauvegarder un template d'email
     *
     * @param string $templateName
     * @param string $content
     * @return bool
     */
    public function saveTemplate(string $templateName, string $content): bool
    {
        try {
            MailConfig::updateOrCreate(
                ['template_name' => $templateName],
                ['template_content' => $content]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Supprimer un template d'email
     *
     * @param string $templateName
     * @return bool
     */
    public function deleteTemplate(string $templateName): bool
    {
        return MailConfig::where('template_name', $templateName)->delete() > 0;
    }

    /**
     * Appliquer les variables au contenu du template
     *
     * @param string $content
     * @param array $variables
     * @return string
     */
    public function applyTemplate(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }

        return $content;
    }

    /**
     * Lister tous les templates disponibles
     *
     * @return array
     */
    public function listTemplates(): array
    {
        return MailConfig::whereNotNull('template_name')
            ->select(['template_name', 'template_content'])
            ->get()
            ->toArray();
    }
}