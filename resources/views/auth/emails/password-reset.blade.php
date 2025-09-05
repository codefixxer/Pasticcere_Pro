<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reimposta Password</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color:#33475b;">
    <table  data-page-length="25"width="100%" bgcolor="#f6f9fc" cellpadding="0" cellspacing="0" role="presentation" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table  data-page-length="25"width="480" bgcolor="#ffffff" cellpadding="0" cellspacing="0" role="presentation" style="border-radius:12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); padding: 40px;">
                    <tr>
                        <td align="center" style="padding-bottom: 32px;">
<img src="{{ config('app.url') }}/assets/images/asset/logo.jpg" alt="Pasticcere Pro" width="150" style="display: block; border-radius: 8px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 20px; font-weight: 700; color: #1a202c; padding-bottom: 16px;">
                            Ciao!
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px; color: #33475b; line-height: 1.5; padding-bottom: 32px;">
                            Hai ricevuto questa email perché abbiamo ricevuto una richiesta di reimpostazione della password per il tuo account.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom: 40px;">
                            <a href="{{ $actionUrl ?? $url ?? '#' }}" target="_blank"
                                style="background-color:#e2ae76; color:#041930; text-decoration:none; padding:14px 40px; border-radius: 30px; font-weight: 600; font-size: 16px; display:inline-block; box-shadow: 0 4px 10px rgba(226,174,118,0.4); transition: background-color 0.3s ease;">
                                Reimposta Password
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #6b7280; line-height: 1.4; padding-bottom: 20px;">
                            Questo link per reimpostare la password scadrà tra 60 minuti.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #6b7280; line-height: 1.4; padding-bottom: 40px;">
                            Se non hai richiesto la reimpostazione della password, nessuna azione è necessaria.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 16px; color: #33475b; line-height: 1.5; padding-bottom: 32px;">
                            Cordiali saluti,<br>
                            <strong>Il team di Pasticcere Pro</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px; color: #9ca3af; line-height: 1.3; border-top: 1px solid #e5e7eb; padding-top: 24px;">
                            Se hai difficoltà a cliccare il pulsante "Reimposta Password", copia e incolla il seguente URL nel tuo browser:<br>
                            <a href="{{ $actionUrl ?? $url ?? '#' }}" target="_blank" style="color: #e2ae76; word-break: break-all;">
                                {{ $actionUrl ?? $url ?? '#' }}
                            </a>
                        </td>
                    </tr>
                </table>
                <p style="font-size: 12px; color: #9ca3af; margin-top: 20px;">© 2025 Pasticcere Pro. Tutti i diritti riservati.</p>
            </td>
        </tr>
    </table>
</body>
</html>
