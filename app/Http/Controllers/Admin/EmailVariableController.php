<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailVariable;
use Illuminate\Http\Request;

class EmailVariableController extends Controller
{
    public function index()
    {
        $variables = EmailVariable::all();
        return response()->json($variables);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:email_variables,name',
            'description' => 'required|string',
            'example' => 'required|string'
        ]);

        $variable = EmailVariable::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Variable créée avec succès',
            'data' => $variable
        ]);
    }

    public function update(Request $request, EmailVariable $variable)
    {
        $request->validate([
            'name' => 'required|string|unique:email_variables,name,' . $variable->id,
            'description' => 'required|string',
            'example' => 'required|string'
        ]);

        $variable->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Variable mise à jour avec succès',
            'data' => $variable
        ]);
    }

    public function destroy(EmailVariable $variable)
    {
        $variable->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variable supprimée avec succès'
        ]);
    }
} 