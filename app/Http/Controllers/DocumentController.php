<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $query = Document::with('uploadedBy');

        if ($user->isLocataire()) {

            if (!$user->locataire) {
                // Aucun profil locataire lié → aucun document
                $documents = collect();
                return view('documents.index', compact('documents'));
            }

            $locataireId = $user->locataire->id;
            $contratIds  = $user->locataire->contrats->pluck('id');

            // Le locataire ne voit QUE :
            //  - les documents attachés directement à son profil Locataire
            //  - les documents attachés à SES contrats
            //  - les fichiers qu'il a lui-même uploadés
            $query->where(function ($q) use ($locataireId, $contratIds, $user) {
                $q->where(function ($q2) use ($locataireId) {
                    $q2->where('documentable_type', 'App\\Models\\Locataire')
                       ->where('documentable_id', $locataireId);
                })
                ->orWhere(function ($q2) use ($contratIds) {
                    $q2->where('documentable_type', 'App\\Models\\Contrat')
                       ->whereIn('documentable_id', $contratIds);
                })
                ->orWhere(function ($q2) use ($user) {
                    $q2->where('documentable_type', 'App\\Models\\User')
                       ->where('documentable_id', $user->id);
                })
                ->orWhere('uploaded_by', $user->id);
            });

        } elseif ($user->isProprietaire()) {

            if (!$user->proprietaire) {
                // Même sans profil lié, il doit voir les documents envoyés à son User
                $query->where('documentable_type', 'App\\Models\\User')
                      ->where('documentable_id', $user->id);
                $documents = $query->latest()->paginate(15);
                return view('documents.index', compact('documents'));
            }

            $proprietaireId = $user->proprietaire->id;
            $bienIds        = $user->proprietaire->biens->pluck('id');
            $contratIds     = \App\Models\Contrat::whereIn('bien_id', $bienIds)->pluck('id');

            // Le propriétaire voit les docs de ses biens, ses contrats, ses uploads et son User
            $query->where(function ($q) use ($proprietaireId, $bienIds, $contratIds, $user) {
                $q->where(function ($q2) use ($proprietaireId) {
                    $q2->where('documentable_type', 'App\\Models\\Proprietaire')
                       ->where('documentable_id', $proprietaireId);
                })
                ->orWhere(function ($q2) use ($bienIds) {
                    $q2->where('documentable_type', 'App\\Models\\Bien')
                       ->whereIn('documentable_id', $bienIds);
                })
                ->orWhere(function ($q2) use ($contratIds) {
                    $q2->where('documentable_type', 'App\\Models\\Contrat')
                       ->whereIn('documentable_id', $contratIds);
                })
                ->orWhere(function ($q2) use ($user) {
                    $q2->where('documentable_type', 'App\\Models\\User')
                       ->where('documentable_id', $user->id);
                })
                ->orWhere('uploaded_by', $user->id);
            });
        }

        // Admin & Gestionnaire → voient tout, pas de filtre

        $documents = $query->latest()->paginate(15);
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
    public function show(Document $document) 
    { 
        // Si ce n'est pas l'uploader qui regarde, on marque comme vu
        if ($document->uploaded_by !== auth()->id() && !$document->viewed_at) {
            $document->update(['viewed_at' => now()]);
        }
        return view('documents.show', compact('document')); 
    }
    public function edit(Document $document) { return view('documents.edit', compact('document')); }
    public function update(Request $request, Document $document) { abort(403); }
}