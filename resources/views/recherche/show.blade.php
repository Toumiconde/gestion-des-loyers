@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row g-5">
        <!-- Galerie Photos -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=1200&q=80" class="img-fluid" alt="{{ $unite->bien->libelle }}">
            </div>
            
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="fw-bold mb-0">{{ $unite->bien->libelle }} - {{ $unite->libelle }}</h1>
                    <span class="badge bg-success rounded-pill px-3 py-2">Disponible</span>
                </div>

                <div class="row g-4 mb-5 text-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-4">
                            <i class="fas fa-bed text-primary mb-2 fa-lg"></i>
                            <div class="fw-bold">{{ $unite->nombre_chambres }}</div>
                            <small class="text-muted">Chambres</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-4">
                            <i class="fas fa-expand-arrows-alt text-primary mb-2 fa-lg"></i>
                            <div class="fw-bold">{{ $unite->surface }} m²</div>
                            <small class="text-muted">Surface</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-4">
                            <i class="fas fa-layer-group text-primary mb-2 fa-lg"></i>
                            <div class="fw-bold">Étage {{ $unite->niveau }}</div>
                            <small class="text-muted">Niveau</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-4">
                            <i class="fas fa-money-bill-wave text-primary mb-2 fa-lg"></i>
                            <div class="fw-bold">{{ number_format($unite->prix_loyer, 0, ',', ' ') }}</div>
                            <small class="text-muted">FG / mois</small>
                        </div>
                    </div>
                </div>

                <h4 class="fw-bold mb-3">Description du logement</h4>
                <p class="text-muted lead">
                    {{ $unite->description ?: "Ce magnifique logement situé à {$unite->bien->adresse} offre tout le confort nécessaire pour une vie paisible. Idéal pour une famille ou un professionnel cherchant un cadre agréable." }}
                </p>

                <hr class="my-5">

                <h4 class="fw-bold mb-4">Équipements et informations</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Électricité disponible</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Eau courante</li>
                            <li class="mb-3"><i class="fas fa-shower text-primary me-2"></i> Douche **{{ ucfirst($unite->bien->type_douche ?? 'Interne') }}**</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Accès sécurisé</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Proche des commerces</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Parking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Action -->
        <div class="col-lg-4">
            <div class="card border-0 shadow rounded-4 p-4 sticky-top" style="top: 2rem;">
                <h3 class="fw-bold mb-4 text-primary">{{ number_format($unite->prix_loyer, 0, ',', ' ') }} FG <small class="text-muted">/ mois</small></h3>
                
                <div class="mb-4">
                    <div class="d-flex align-items-center p-3 bg-light rounded-4 border">
                        <img src="{{ $unite->bien->proprietaire->user->avatar ?? 'https://ui-avatars.com/api/?name='.$unite->bien->proprietaire->user->name }}" class="rounded-circle me-3" width="50" height="50">
                        <div>
                            <div class="fw-bold">{{ $unite->bien->proprietaire->user->name }}</div>
                            <small class="text-muted">Propriétaire</small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 rounded-4">
                    <i class="fas fa-info-circle me-2"></i> Intéressé par ce logement ? Postulez dès maintenant. Le propriétaire et l'admin seront notifiés de votre choix.
                </div>

                <form action="{{ route('recherche.postuler', $unite) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Votre message (optionnel)</label>
                        <textarea name="message" class="form-control border-0 bg-light rounded-3" rows="3" placeholder="Ex: Bonjour, je suis très intéressé par ce logement..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-3">
                        <i class="fas fa-paper-plane me-2"></i> Postuler maintenant
                    </button>
                    <p class="text-center small text-muted mb-0">Aucun paiement requis à cette étape.</p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
