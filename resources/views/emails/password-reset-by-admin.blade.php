<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe — GESTLOYER</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; padding: 40px 20px; }
        .wrapper { max-width: 600px; margin: 0 auto; }

        .header {
            background: linear-gradient(135deg, #02132D 0%, #1e3a8a 100%);
            border-radius: 24px 24px 0 0;
            padding: 40px;
            text-align: center;
        }
        .logo-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.1);
            border-radius: 18px;
            margin-bottom: 16px;
            font-size: 28px;
        }
        .header h1 { color: #fff; font-size: 22px; font-weight: 900; letter-spacing: -0.5px; margin-bottom: 6px; }
        .header p { color: #94a3b8; font-size: 13px; }

        .body {
            background: #ffffff;
            padding: 40px;
        }

        .greeting { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #64748b; line-height: 1.7; margin-bottom: 32px; }

        .password-box {
            background: linear-gradient(135deg, #fefce8, #fef9c3);
            border: 2px solid #fde047;
            border-radius: 18px;
            padding: 28px;
            text-align: center;
            margin-bottom: 28px;
        }
        .password-box .label {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #92400e;
            margin-bottom: 12px;
        }
        .password-box .password {
            font-size: 28px;
            font-weight: 900;
            color: #1e293b;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
            background: white;
            padding: 14px 28px;
            border-radius: 12px;
            display: inline-block;
            border: 1px solid #fde047;
            margin-bottom: 12px;
        }
        .password-box .note {
            font-size: 11px;
            color: #92400e;
            font-weight: 700;
        }

        .steps {
            background: #f8fafc;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 28px;
        }
        .steps h3 { font-size: 13px; font-weight: 900; color: #334155; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
        .step { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
        .step-num {
            width: 26px; height: 26px; border-radius: 8px;
            background: #1e3a8a; color: white;
            font-size: 11px; font-weight: 900;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .step p { font-size: 13px; color: #475569; line-height: 1.5; }
        .step strong { color: #1e293b; }

        .cta {
            text-align: center;
            margin-bottom: 28px;
        }
        .cta a {
            display: inline-block;
            background: #1e3a8a;
            color: white !important;
            text-decoration: none;
            padding: 14px 40px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .warning {
            background: #fff7ed;
            border-left: 4px solid #f97316;
            border-radius: 0 12px 12px 0;
            padding: 16px 20px;
            margin-bottom: 28px;
        }
        .warning p { font-size: 12px; color: #9a3412; line-height: 1.6; }

        .footer {
            background: #f8fafc;
            border-radius: 0 0 24px 24px;
            padding: 28px 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p { font-size: 11px; color: #94a3b8; line-height: 1.7; }
        .footer strong { color: #64748b; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- HEADER --}}
    <div class="header">
        <div class="logo-badge">🏢</div>
        <h1>GESTLOYER</h1>
        <p>Plateforme de Gestion Immobilière Professionnelle</p>
    </div>

    {{-- BODY --}}
    <div class="body">
        <p class="greeting">Bonjour {{ $user->name }},</p>
        <p class="intro">
            L'administrateur de la plateforme <strong>GESTLOYER</strong> a réinitialisé votre mot de passe.
            Voici vos nouveaux accès pour vous connecter à votre espace personnel.
        </p>

        {{-- MOT DE PASSE --}}
        <div class="password-box">
            <p class="label">🔑 Votre nouveau mot de passe temporaire</p>
            <div class="password">{{ $newPassword }}</div>
            <p class="note">⚠️ Ce mot de passe est temporaire. Changez-le dès votre première connexion.</p>
        </div>

        {{-- ÉTAPES --}}
        <div class="steps">
            <h3>Comment vous connecter ?</h3>
            <div class="step">
                <div class="step-num">1</div>
                <p>Rendez-vous sur <strong>{{ config('app.url') }}</strong></p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <p>Entrez votre email : <strong>{{ $user->email }}</strong></p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <p>Entrez le mot de passe temporaire ci-dessus</p>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <p>Allez dans <strong>Mon Profil → Sécurité</strong> pour définir votre propre mot de passe</p>
            </div>
        </div>

        {{-- BOUTON CTA --}}
        <div class="cta">
            <a href="{{ config('app.url') }}/login">Accéder à la plateforme →</a>
        </div>

        {{-- AVERTISSEMENT --}}
        <div class="warning">
            <p>
                <strong>🛡️ Sécurité :</strong> Si vous n'avez pas demandé de réinitialisation de mot de passe,
                contactez immédiatement l'administrateur de votre agence. Ne partagez jamais votre mot de passe
                avec qui que ce soit.
            </p>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>
            Cet email a été envoyé automatiquement par <strong>GESTLOYER Administration</strong>.<br>
            © {{ date('Y') }} GESTLOYER — Tous droits réservés.<br>
            <strong>{{ config('app.url') }}</strong>
        </p>
    </div>

</div>
</body>
</html>
