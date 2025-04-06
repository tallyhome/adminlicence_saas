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
        $template->subject = $validated['subject'];
        $template->content = $validated['content'];
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

        $template->update([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
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
        $testData = array_combine(
            array_map(function ($var) { return '{' . $var . '}'; }, array_keys($variables)),
            array_map(function ($var) { return '[' . $var . ']'; }, array_keys($variables))
        );

        $subject = $template->subject[$language] ?? $template->subject['en'];
        $content = $template->content[$language] ?? $template->content['en'];

        // Remplacer les variables par des valeurs de test
        $content = strtr($content, $testData);

        return view('admin.email.templates.preview', compact('subject', 'content'));
    }
}