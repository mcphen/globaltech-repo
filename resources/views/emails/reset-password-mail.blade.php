<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réinitialisation de mot de passe</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { background-color: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; }
        .warning { background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TongoloTech</h1>
        </div>
        
        <div class="content">
            <h2>Bonjour !</h2>
            
            <p>Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" class="button" style="color: white;">
                    Réinitialiser le mot de passe
                </a>
            </div>
            
            <div class="warning">
                <p><strong>Attention :</strong> Ce lien de réinitialisation expirera à <strong>{{ $expires }}</strong> (dans 60 minutes).</p>
            </div>
            
            <p>Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune action supplémentaire n'est requise.</p>
            
            <p><strong>Conseil de sécurité :</strong> Ne partagez jamais ce lien avec qui que ce soit.</p>
            
            <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">
            
            <p><small>Si le bouton "Réinitialiser le mot de passe" ne fonctionne pas, copiez et collez l'URL ci-dessous dans votre navigateur :</small></p>
            <p style="word-break: break-all; background-color: #f3f4f6; padding: 10px; border-radius: 4px; font-size: 12px;">
                {{ $resetUrl }}
            </p>
        </div>
        
        <div class="footer">
            <p>Cordialement,<br>L'équipe TongoloTech</p>
            <p>© {{ date('Y') }} TongoloTech. Tous droits réservés.</p>
            <p><small>Cet email a été envoyé à {{ $email }}</small></p>
        </div>
    </div>
</body>
</html>