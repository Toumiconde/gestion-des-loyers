<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('uploadedBy')->paginate(10);
        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fichier'            => 'required|file|mimes:jpg,png,pdf|max:5120',
            'nom'                => 'required|string|max:200',
            'type'               => 'required|in:contrat_pdf,quittance,photo,piece_identite,autre',
            'documentable_type'  => 'required|string',
            'documentable_id'    => 'required|integer',
        ]);

        // Stockage du fichier dans storage/app/public/documents
        $chemin = $request->file('fichier')->store('documents', 'public');

        Document::create([
            'documentable_type' => $request->documentable_type,
            'documentable_id'   => $request->documentable_id,
            'nom'               => $request->nom,
            'type'              => $request->type,
            'chemin'            => $chemin,
            'taille_ko'         => round($request->file('fichier')->getSize() / 1024),
            'uploaded_by'       => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Document uploadé avec succès.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->chemin);
        $document->delete();
        return redirect()->back()->with('success', 'Document supprimé.');
    }

    public function create() { return view('documents.create'); }
    public function show(Document $document) { return view('documents.show', compact('document')); }
    public function edit(Document $document) { return view('documents.edit', compact('document')); }
    public function update(Request $request, Document $document) { abort(403); }
}