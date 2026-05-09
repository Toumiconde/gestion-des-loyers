@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold text-primary mb-2">Trouvez votre futur chez-vous</h1>
            <p class="lead text-muted">Explorez nos logements disponibles et faites votre choix en un clic.</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm border-0 mb-5 p-4 bg-white rounded-4">
        <form action="{{ route('recherche.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Prix maximum (FG)</label>
                <input type="number" name="prix_max" class="form-control form-control-lg bg-light border-0" placeholder="Ex: 150000" value="{{ request('prix_max') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Nombre de chambres min.</label>
                <select name="chambres" class="form-select form-select-lg bg-light border-0">
                    <option value="">Peu importe</option>
                    @for($i=1; $i<=5; $i++)
                        <option value="{{ $i }}" {{ request('chambres') == $i ? 'selected' : '' }}>{{ $i }}+ chambres</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">
                    <i class="fas fa-search me-2"></i> Rechercher
                </button>
            </div>
        </form>
    </div>

    <!-- Résultats -->
    <div class="row g-4">
        @forelse($unites as $unite)
        <div class="col-xl-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all rounded-4 overflow-hidden">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=800&q=80" class="card-img-top" alt="{{ $unite->bien->libelle }}" style="height: 240px; object-fit: cover;">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">Disponible</span>
                    </div>
                    <div class="position-absolute bottom-0 start-0 m-3">
                        <span class="badge bg-white text-dark rounded-pill px-3 py-2 shadow-sm fw-bold">
                            {{ number_format($unite->prix_loyer, 0, ',', ' ') }} FG / mois
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h4 class="card-title fw-bold mb-0">{{ $unite->bien->libelle }}</h4>
                        <span class="text-primary fw-bold">{{ $unite->libelle }}</span>
                    </div>
                    <p class="text-muted mb-3"><i class="fas fa-map-marker-alt me-2"></i>{{ $unite->bien->adresse }}</p>
                    
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-light rounded-3">
                                <i class="fas fa-bed text-primary me-2"></i>
                                <span>{{ $unite->nombre_chambres }} Ch.</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-light rounded-3">
                                <i class="fas fa-expand-arrows-alt text-primary me-2"></i>
                                <span>{{ $unite->surface }} m²</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-light rounded-3">
                                <i class="fas fa-shower text-primary me-2"></i>
                                <span>Douche {{ ucfirst($unite->bien->type_douche ?? 'Interne') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-light rounded-3">
                                <i class="fas fa-layer-group text-primary me-2"></i>
                                <span>Étage {{ $unite->niveau }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-4 mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $unite->bien->proprietaire->user->avatar ?? 'https://ui-avatars.com/api/?name='.$unite->bien->proprietaire->user->name }}" class="rounded-circle me-2" width="32" height="32">
                                <small class="text-muted">{{ $unite->bien->proprietaire->user->name }}</small>
                            </div>
                            <a href="{{ route('recherche.show', $unite) }}" class="btn btn-outline-primary rounded-pill px-4">Détails</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="mb-4">
                <i class="fas fa-home fa-4x text-light"></i>
            </div>
            <h3>Aucun logement disponible pour le moment</h3>
            <p class="text-muted">Revenez plus tard ou ajustez vos filtres de recherche.</p>
            <a href="{{ route('recherche.index') }}" class="btn btn-primary rounded-pill px-4 mt-3">Réinitialiser les filtres</a>
        </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $unites->links() }}
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
}
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection
