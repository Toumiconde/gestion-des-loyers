@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row mb-5">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold text-primary mb-2">Demandes de Location</h1>
            <p class="lead text-muted">Gérez les candidatures pour vos logements disponibles.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 py-3">Logement</th>
                        <th class="border-0 py-3">Locataire</th>
                        <th class="border-0 py-3">Date</th>
                        <th class="border-0 py-3">Statut</th>
                        <th class="text-end pe-4 border-0 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($demandes as $demande)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="position-relative me-3">
                                    <img src="{{ $demande->uniteLocative->bien->main_photo }}" 
                                         class="rounded-3 shadow-sm" width="60" height="45" style="object-fit: cover;">
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $demande->uniteLocative->bien->libelle }}</div>
                                    <small class="text-muted">{{ $demande->uniteLocative->libelle }} (Étage {{ $demande->uniteLocative->niveau }})</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $demande->user->avatar ?? 'https://ui-avatars.com/api/?name='.$demande->user->name }}" class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <div class="fw-bold">{{ $demande->user->name }}</div>
                                    <small class="text-muted">{{ $demande->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted">{{ $demande->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td>
                            @php
                                $badgeClass = [
                                    'en_attente' => 'bg-warning text-dark',
                                    'valide_proprietaire' => 'bg-info text-white',
                                    'accepte' => 'bg-success text-white',
                                    'rejete' => 'bg-danger text-white',
                                    'paye' => 'bg-primary text-white',
                                ][$demande->statut] ?? 'bg-secondary text-white';

                                $statutLabel = [
                                    'en_attente' => 'En attente',
                                    'valide_proprietaire' => 'Validé Proprio',
                                    'accepte' => 'Accepté',
                                    'rejete' => 'Rejeté',
                                    'paye' => 'Payé',
                                ][$demande->statut] ?? $demande->statut;
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 {{ $badgeClass }}">{{ $statutLabel }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-pill">
                                @if(Auth::user()->role === 'proprietaire' && $demande->statut === 'en_attente')
                                    <form action="{{ route('demandes-location.valider-proprietaire', $demande) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-white text-success border-end" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                @if(in_array(Auth::user()->role, ['admin', 'gestionnaire']) && ($demande->statut === 'en_attente' || $demande->statut === 'valide_proprietaire'))
                                    <form action="{{ route('demandes-location.valider-admin', $demande) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-white text-primary border-end" title="Validation Finale Admin">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($demande->statut === 'en_attente' || $demande->statut === 'valide_proprietaire')
                                    <form action="{{ route('demandes-location.rejeter', $demande) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-white text-danger" title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <button type="button" class="btn btn-white text-muted" data-bs-toggle="modal" data-bs-target="#modalDemande{{ $demande->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="modalDemande{{ $demande->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">Détails de la demande</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start p-4">
                                            <img src="{{ $demande->uniteLocative->bien->main_photo }}" 
                                                 class="img-fluid rounded-4 mb-4 shadow-sm" alt="Aperçu">

                                            <p class="fw-bold mb-1">Message du locataire :</p>
                                            <div class="bg-light p-3 rounded-4 mb-4">
                                                {{ $demande->message ?: "Aucun message fourni." }}
                                            </div>
                                            
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <p class="small text-muted mb-0">Prix proposé :</p>
                                                    <p class="fw-bold">{{ number_format($demande->uniteLocative->prix_loyer, 0, ',', ' ') }} FG</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="small text-muted mb-0">Chambres :</p>
                                                    <p class="fw-bold">{{ $demande->uniteLocative->nombre_chambres }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <p class="text-muted mb-0">Aucune demande trouvée.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $demandes->links() }}
    </div>
</div>
@endsection
