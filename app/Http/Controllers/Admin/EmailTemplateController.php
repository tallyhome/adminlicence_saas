<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EmailTemplateController extends Controller
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function index()
    {
        $templates = EmailTemplate::paginate(10);
        $languages = $this->translationService->getAvailableLanguages();
        return view('admin.email.templates.index', compact('templates', 'languages'));
    }

    public function create()
    {
        $languages = $this->translationService->getAvailableLanguages();
        return view('admin.email.templates.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates',
            'subject' => 'required|array',
            'content' => 'required|array',
            'variables' => 'nullable|json',
            'description' => 'nullable|string',
            'is_system' => 'boolean'
        ]);

        $template = new EmailTemplate();
        $template->name = $validated['name'];
        $template->subject = json_encode($validated['subject']);
        $template->content = json_encode($validated['content']);
        
        // Récupérer le contenu HTML de la première langue non vide
        $htmlContent = '';
        foreach ($validated['content'] as $langContent) {
            if (!empty($langContent)) {
                $htmlContent = $langContent;
                break;
            }
        }
        $template->html_content = $htmlContent;
        $template->text_content = strip_tags($htmlContent);
        
        $template->variables = $validated['variables'] ?? '[]';
        $template->description = $validated['description'] ?? null;
        $template->is_system = $validated['is_system'] ?? false;
        $template->save();

        return redirect()->route('admin.email.templates.index')
            ->with('success', 'Template créé avec succès');
    }

    public function edit(EmailTemplate $template)
    {
        $languages = $this->translationService->getAvailableLanguages();
        return view('admin.email.templates.edit', compact('template', 'languages'));
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name,' . $template->id,
            'subject' => 'required|array',
            'content' => 'required|array',
            'variables' => 'nullable|json',
            'description' => 'nullable|string',
            'is_system' => 'boolean'
        ]);

        // Récupérer le contenu HTML de la première langue non vide
        $htmlContent = '';
        foreach ($validated['content'] as $langContent) {
            if (!empty($langContent)) {
                $htmlContent = $langContent;
                break;
            }
        }

        $template->update([
            'name' => $validated['name'],
            'subject' => json_encode($validated['subject']),
            'content' => json_encode($validated['content']),
            'html_content' => $htmlContent,
            'text_content' => strip_tags($htmlContent),
            'variables' => $validated['variables'] ?? '[]',
            'description' => $validated['description'] ?? null,
            'is_system' => $validated['is_system'] ?? false
        ]);

        return redirect()->route('admin.email.templates.index')
            ->with('success', 'Template mis à jour avec succès');
    }

    public function destroy(EmailTemplate $template)
    {
        if ($template->is_system) {
            return back()->with('error', 'Impossible de supprimer un template système');
        }

        $template->delete();

        return redirect()->route('admin.email.templates.index')
            ->with('success', 'Template supprimé avec succès');
    }

    public function preview(Request $request, EmailTemplate $template)
    {
        $language = $request->get('language', app()->getLocale());
        $variables = json_decode($template->variables, true) ?? [];
        
        // Créer des données de test plus réalistes
        $testData = [];
        foreach (array_keys($variables) as $var) {
            $placeholder = '{' . $var . '}';
            
            // Générer des valeurs d'exemple selon le nom de la variable
            switch (strtolower($var)) {
                case 'nom':
                case 'name':
                    $testData[$placeholder] = 'Jean Dupont';
                    break;
                case 'email':
                    $testData[$placeholder] = 'exemple@domaine.com';
                    break;
                case 'date':
                    $testData[$placeholder] = date('d/m/Y');
                    break;
                case 'licence':
                case 'license':
                    $testData[$placeholder] = 'XXXX-XXXX-XXXX-XXXX';
                    break;
                case 'entreprise':
                case 'company':
                    $testData[$placeholder] = 'Entreprise SAS';
                    break;
                default:
                    $testData[$placeholder] = '[' . $var . ']';
            }
        }

        // Décoder les champs JSON avant d'accéder aux éléments
        $subjectData = json_decode($template->subject, true);
        $contentData = json_decode($template->content, true);
        
        $subject = $subjectData[$language] ?? ($subjectData['en'] ?? '');
        $content = $contentData[$language] ?? ($contentData['en'] ?? '');

        // Remplacer les variables par des valeurs de test
        $content = strtr($content, $testData);
        $subject = strtr($subject, $testData);

        return view('admin.email.templates.preview', compact('subject', 'content', 'testData'));
    }
}