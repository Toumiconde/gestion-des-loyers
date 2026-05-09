<x-mail::message>
# Félicitations !

Bonjour {{ $demande->user->name }},

Bonne nouvelle ! Le propriétaire **{{ $demande->uniteLocative->bien->proprietaire->user->name }}** a accepté votre demande pour le logement suivant :

- **Logement :** {{ $demande->uniteLocative->bien->libelle }}
- **Niveau :** {{ $demande->uniteLocative->libelle }} (Étage {{ $demande->uniteLocative->niveau }})
- **Loyer :** {{ number_format($demande->uniteLocative->prix_loyer, 0, ',', ' ') }} FG
- **Adresse :** {{ $demande->uniteLocative->bien->adresse }}

Vous pouvez maintenant procéder au versement pour confirmer votre réservation.

<x-mail::button :url="route('paiements.create', ['demande_id' => $demande->id])">
Passer au versement
</x-mail::button>

Merci de votre confiance,<br>
L'équipe {{ config('app.name') }}

</x-mail::message>
