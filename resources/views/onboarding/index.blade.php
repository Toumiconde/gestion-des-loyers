<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GESTLOYER — Choisissez votre profil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            position: relative;
        }

        /* Cercles décoratifs en arrière-plan */
        body::before {
            content: '';
            position: fixed;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%);
            top: -200px; right: -200px;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 70%);
            bottom: -150px; left: -150px;
            pointer-events: none;
        }

        .container {
            max-width: 900px;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        /* HEADER */
        .header {
            text-align: center;
            margin-bottom: 48px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .logo-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            box-shadow: 0 8px 32px rgba(59,130,246,0.4);
        }

        .logo-text {
            font-size: 28px;
            font-weight: 900;
            color: white;
            letter-spacing: -0.5px;
        }

        .logo-text span {
            color: #3b82f6;
        }

        .header h1 {
            font-size: 42px;
            font-weight: 900;
            color: white;
            line-height: 1.1;
            margin-bottom: 12px;
        }

        .header p {
            font-size: 17px;
            color: rgba(255,255,255,0.55);
            font-weight: 500;
        }

        .user-greeting {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 50px;
            padding: 8px 20px;
            margin-bottom: 24px;
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            font-weight: 600;
        }

        .user-greeting i { color: #3b82f6; }

        /* CARDS */
        .cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 640px) {
            .cards { grid-template-columns: 1fr; }
            .header h1 { font-size: 30px; }
        }

        .card-btn {
            all: unset;
            cursor: pointer;
            display: block;
            width: 100%;
        }

        .card {
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 28px;
            padding: 40px 36px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 28px;
            opacity: 0;
            transition: opacity 0.4s;
        }

        .card.locataire::before {
            background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05));
        }

        .card.proprietaire::before {
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(99,102,241,0.05));
        }

        .card:hover {
            transform: translateY(-6px) scale(1.02);
            border-color: rgba(255,255,255,0.25);
        }

        .card.locataire:hover {
            border-color: rgba(59,130,246,0.6);
            box-shadow: 0 24px 60px rgba(59,130,246,0.2), 0 0 0 1px rgba(59,130,246,0.3);
        }

        .card.proprietaire:hover {
            border-color: rgba(99,102,241,0.6);
            box-shadow: 0 24px 60px rgba(99,102,241,0.2), 0 0 0 1px rgba(99,102,241,0.3);
        }

        .card:hover::before { opacity: 1; }

        .card-icon {
            width: 72px; height: 72px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 24px;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            z-index: 1;
        }

        .card:hover .card-icon { transform: scale(1.15) rotate(-5deg); }

        .card.locataire .card-icon {
            background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.1));
            color: #60a5fa;
            border: 1px solid rgba(59,130,246,0.3);
        }

        .card.proprietaire .card-icon {
            background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(99,102,241,0.1));
            color: #818cf8;
            border: 1px solid rgba(99,102,241,0.3);
        }

        .card-title {
            font-size: 26px;
            font-weight: 900;
            color: white;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .card-desc {
            font-size: 14px;
            color: rgba(255,255,255,0.5);
            line-height: 1.65;
            margin-bottom: 28px;
            position: relative;
            z-index: 1;
        }

        .card-features {
            list-style: none;
            margin-bottom: 32px;
            position: relative;
            z-index: 1;
        }

        .card-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: rgba(255,255,255,0.6);
            padding: 6px 0;
            font-weight: 500;
        }

        .card-features li i {
            font-size: 11px;
            width: 20px; height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card.locataire .card-features li i {
            background: rgba(59,130,246,0.2);
            color: #60a5fa;
        }

        .card.proprietaire .card-features li i {
            background: rgba(99,102,241,0.2);
            color: #818cf8;
        }

        .card-cta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 14px;
            padding: 16px 20px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s;
            position: relative;
            z-index: 1;
        }

        .card.locataire .card-cta {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 20px rgba(59,130,246,0.4);
        }

        .card.proprietaire .card-cta {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            box-shadow: 0 4px 20px rgba(99,102,241,0.4);
        }

        .card:hover .card-cta { filter: brightness(1.1); transform: translateY(-1px); }

        .card-cta .arrow {
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s;
        }

        .card:hover .card-cta .arrow { transform: translateX(4px); }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 36px;
            color: rgba(255,255,255,0.25);
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <div class="logo-icon"><i class="fa-solid fa-building-shield"></i></div>
                <div class="logo-text">GEST<span>LOYER</span></div>
            </div>

            <div class="user-greeting">
                <i class="fa-solid fa-circle-check"></i>
                Connecté en tant que {{ auth()->user()->name }}
            </div>

            <h1>Quel est votre profil ?</h1>
            <p>Choisissez votre rôle pour accéder à votre espace personnalisé</p>
        </div>

        <div class="cards">
            {{-- LOCATAIRE --}}
            <form action="{{ route('onboarding.select') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="locataire">
                <button type="submit" class="card-btn">
                    <div class="card locataire">
                        <div class="card-icon">
                            <i class="fa-solid fa-house-user"></i>
                        </div>
                        <div class="card-title">Je suis Locataire</div>
                        <div class="card-desc">Je cherche un logement ou je suis déjà locataire et je veux gérer mon espace.</div>
                        <ul class="card-features">
                            <li><i class="fa-solid fa-check"></i> Consulter mon contrat de bail</li>
                            <li><i class="fa-solid fa-check"></i> Télécharger mes quittances</li>
                            <li><i class="fa-solid fa-check"></i> Suivre mes paiements</li>
                            <li><i class="fa-solid fa-check"></i> Signaler un incident</li>
                        </ul>
                        <div class="card-cta">
                            Choisir ce profil
                            <div class="arrow"><i class="fa-solid fa-arrow-right" style="font-size:12px"></i></div>
                        </div>
                    </div>
                </button>
            </form>

            {{-- PROPRIÉTAIRE --}}
            <form action="{{ route('onboarding.select') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="proprietaire">
                <button type="submit" class="card-btn">
                    <div class="card proprietaire">
                        <div class="card-icon">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div class="card-title">Je suis Propriétaire</div>
                        <div class="card-desc">Je possède un ou plusieurs biens et je souhaite les mettre en gestion locative.</div>
                        <ul class="card-features">
                            <li><i class="fa-solid fa-check"></i> Gérer mes biens immobiliers</li>
                            <li><i class="fa-solid fa-check"></i> Suivre mes revenus locatifs</li>
                            <li><i class="fa-solid fa-check"></i> Superviser mes contrats</li>
                            <li><i class="fa-solid fa-check"></i> Consulter mon bilan financier</li>
                        </ul>
                        <div class="card-cta">
                            Choisir ce profil
                            <div class="arrow"><i class="fa-solid fa-arrow-right" style="font-size:12px"></i></div>
                        </div>
                    </div>
                </button>
            </form>
        </div>

        <div class="footer">
            <p>En choisissant un profil, vous acceptez les conditions générales d'utilisation de GESTLOYER.</p>
        </div>
    </div>
</body>
</html>
