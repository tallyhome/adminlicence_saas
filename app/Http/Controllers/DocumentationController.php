<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DocumentationController extends Controller
{
    public function index()
    {
        return view('documentation.index');
    }

    public function apiIntegration()
    {
        $markdownPath = base_path('docs/API_INTEGRATION.md');
        
        if (!File::exists($markdownPath)) {
            abort(404, 'Documentation not found');
        }
        
        $markdown = File::get($markdownPath);
        
        // Conversion manuelle simple du Markdown en HTML
        // Titres
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $markdown);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $html);
        
        // Code blocks
        $html = preg_replace('/```(\w+)\n([\s\S]*?)```/m', '<pre><code class="language-$1">$2</code></pre>', $html);
        $html = preg_replace('/```([\s\S]*?)```/m', '<pre><code>$1</code></pre>', $html);
        
        // Inline code
        $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
        
        // Liens
        $html = preg_replace('/\[([^\]]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>', $html);
        
        // Listes
        $html = preg_replace('/^- (.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.+<\/li>\n)+/', '<ul>$0</ul>', $html);
        
        // Paragraphes
        $html = preg_replace('/^(?!<h|<ul|<pre|<li|<table)(.+)$/m', '<p>$1</p>', $html);
        
        // Tableaux (conversion simplifiée)
        $html = preg_replace('/\|([^\|]+)\|([^\|]+)\|([^\|]+)\|([^\|]+)\|/m', '<tr><td>$1</td><td>$2</td><td>$3</td><td>$4</td></tr>', $html);
        $html = preg_replace('/<tr>(.+)<\/tr>\n<tr>\s*[-|\s]+<\/tr>/', '<thead>$0</thead><tbody>', $html);
        $html = preg_replace('/(<tr>.+<\/tr>\n)+/', '<table class="table table-bordered">$0</table>', $html);
        
        return view('documentation.api', [
            'content' => $html
        ]);
    }
}
