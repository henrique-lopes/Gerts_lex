<?php

namespace App\Http\Controllers;

use App\Models\CaseDocument;
use App\Models\CaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CaseDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = CaseDocument::with("case")->paginate(10);
        return view("tenant.case_documents.index", compact("documents"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cases = CaseModel::all();
        return view("tenant.case_documents.create", compact("cases"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "case_id" => "required|exists:cases,id",
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "file" => "required|file|max:10240", // Max 10MB
        ]);

        $filePath = $request->file("file")->store("case_documents", "public");

        CaseDocument::create([
            "tenant_id" => auth()->user()->tenant_id,
            "case_id" => $request->case_id,
            "title" => $request->title,
            "description" => $request->description,
            "file_path" => $filePath,
        ]);

        return redirect()->route("tenant.case_documents.index")
            ->with("success", "Documento de caso adicionado com sucesso!");
    }

    /**
     * Display the specified resource.
     */
    public function show(CaseDocument $document)
    {
        return Storage::disk("public")->download($document->file_path, $document->title);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CaseDocument $document)
    {
        Storage::disk("public")->delete($document->file_path);
        $document->delete();

        return redirect()->route("tenant.case_documents.index")
            ->with("success", "Documento de caso removido com sucesso!");
    }
}