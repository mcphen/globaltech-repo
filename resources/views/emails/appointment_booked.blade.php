<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8"> <!-- utf-8 works for most cases -->
  <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
  <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
  <title>Nouveau rendez-vous</title> <!-- The title tag shows in email notifications, like Android 4.4. -->

  <!-- Web Font / @font-face : BEGIN -->
  <!--[if mso]>
  <style>* {font-family: Arial, sans-serif !important;}</style>
  <![endif]-->
  <!-- Web Font / @font-face : END -->

  <!-- CSS Reset : BEGIN -->
  <style>
    html,body { margin:0 auto !important; padding:0 !important; height:100% !important; width:100% !important; }
    * { -ms-text-size-adjust:100%; -webkit-text-size-adjust:100%; }
    div[style*="margin: 16px 0"] { margin:0 !important; }
    table, td { mso-table-lspace:0pt !important; mso-table-rspace:0pt !important; }
    table { border-spacing:0 !important; border-collapse:collapse !important; table-layout:fixed !important; margin:0 auto !important; }
    img { -ms-interpolation-mode:bicubic; }
    a { text-decoration:none; }
    .bg-light { background-color:#f6f9fc; }
    .card { background:#ffffff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .btn { display:inline-block; padding:12px 18px; background:#0ea5e9; color:#ffffff !important; border-radius:6px; font-weight:600; }
    .text-muted { color:#64748b; }
    .badge { display:inline-block; padding:6px 10px; border-radius:999px; background:#e2e8f0; color:#0f172a; font-size:12px; font-weight:600; }
  </style>
  <!-- CSS Reset : END -->
</head>
<body width="100%" style="margin: 0; mso-line-height-rule: exactly;" class="bg-light">
  <center role="article" aria-roledescription="email" lang="fr" style="width: 100%; background-color:#f6f9fc;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width:640px; margin:0 auto;">
      <tr>
        <td style="padding: 24px 16px; text-align:center;">
          <a href="{{ config('app.url') }}" style="font-size:20px; font-weight:700; color:#0f172a;">{{ config('app.name', 'GlobalTECH EDUCATION Africa') }}</a>
        </td>
      </tr>

      <tr>
        <td style="padding: 0 16px 32px 16px;">
          <table role="presentation" width="100%" class="card">
            <tr>
              <td style="padding: 28px 24px 8px 24px; border-bottom:1px solid #f1f5f9;">
                <h1 style="margin:0; font-size:22px; color:#0f172a;">Nouveau rendez-vous reçu</h1>
                <p class="text-muted" style="margin:8px 0 0 0; font-size:14px;">Vous avez reçu une nouvelle demande de rendez-vous.</p>
              </td>
            </tr>

            <tr>
              <td style="padding: 20px 24px;">
                <table role="presentation" width="100%">
                  <tr>
                    <td style="padding-bottom:12px;">
                      <span class="badge">Infos client</span>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding-bottom:6px; color:#0f172a;">
                      <strong>Nom</strong><br>
                      {{ $client?->first_name }} {{ $client?->last_name }}
                    </td>
                  </tr>
                  <tr>
                    <td style="padding-bottom:6px; color:#0f172a;">
                      <strong>Email</strong><br>
                      <a href="mailto:{{ $client?->email }}" style="color:#0ea5e9;">{{ $client?->email }}</a>
                    </td>
                  </tr>
                  @if(!empty($client?->phone))
                  <tr>
                    <td style="padding-bottom:6px; color:#0f172a;">
                      <strong>Téléphone</strong><br>
                      <a href="tel:{{ $client?->phone }}" style="color:#0ea5e9;">{{ $client?->phone }}</a>
                    </td>
                  </tr>
                  @endif
                  @if(!empty($client?->address))
                  <tr>
                    <td style="padding-bottom:6px; color:#0f172a;">
                      <strong>Adresse</strong><br>
                      {{ $client?->address }}
                    </td>
                  </tr>
                  @endif
                </table>

                <table role="presentation" width="100%" style="margin-top:16px;">
                  <tr>
                    <td style="padding-bottom:12px;">
                      <span class="badge">Détails du rendez-vous</span>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding-bottom:6px; color:#0f172a;">
                      <strong>Sujet</strong><br>
                      {{ $appointment->subject }}
                    </td>
                  </tr>
                  <tr>
                    <td style="padding-bottom:6px; color:#0f172a;">
                      <strong>Date et heure</strong><br>
                      @if($schedule)
                        {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l d F Y') }}
                        à {{ substr((string)$schedule->start_time,0,5) }} - {{ substr((string)$schedule->end_time,0,5) }}
                      @else
                        À confirmer
                      @endif
                    </td>
                  </tr>
                  @if(isset($services) && $services->count())
                  <tr>
                    <td style="padding-bottom:6px; color:#0f172a;">
                      <strong>Services</strong><br>
                      <ul style="margin:6px 0 0 18px; padding:0;">
                        @foreach($services as $service)
                          <li style="margin:4px 0;">{{ $service->name }}</li>
                        @endforeach
                      </ul>
                    </td>
                  </tr>
                  @endif
                  @if(!empty($appointment->description))
                  <tr>
                    <td style="padding-bottom:6px; color:#0f172a;">
                      <strong>Description</strong><br>
                      <p style="margin:6px 0 0 0; line-height:1.5;">{{ $appointment->description }}</p>
                    </td>
                  </tr>
                  @endif
                </table>

                <div style="margin-top:20px;">
                  <a class="btn" href="{{ url('/admin/appointments') }}">Voir dans le panneau d'administration</a>
                </div>

                <p class="text-muted" style="margin-top:16px; font-size:12px;">Cet email vous informe de l'identité de la personne ayant pris rendez-vous.</p>
              </td>
            </tr>

            <tr>
              <td style="background:#f8fafc; padding:16px 24px; border-top:1px solid #f1f5f9; text-align:center; color:#64748b; font-size:12px;">
                © {{ date('Y') }} {{ config('app.name', 'GlobalTECH EDUCATION Africa') }}. Tous droits réservés.
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </center>
</body>
</html>
