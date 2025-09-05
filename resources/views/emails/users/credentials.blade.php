<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Benvenuto</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f9fc; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color:#33475b;">
  <table  data-page-length="25"width="100%" bgcolor="#f6f9fc" cellpadding="0" cellspacing="0" role="presentation" style="padding:40px 0;">
    <tr>
      <td align="center">
        <table  data-page-length="25"width="520" bgcolor="#ffffff" cellpadding="0" cellspacing="0" role="presentation" style="border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08); padding:40px;">
          <tr>
            <td align="center" style="padding-bottom:28px;">
              <img src="{{ $appUrl }}/assets/images/asset/logo.jpg" alt="Pasticcere Pro" width="150" style="display:block; border-radius:8px;">
            </td>
          </tr>

          <tr>
            <td style="font-size:20px; font-weight:700; color:#1a202c; padding-bottom:8px;">
              Ciao {{ $name }},
            </td>
          </tr>

          <tr>
            <td style="font-size:16px; line-height:1.6; color:#33475b; padding-bottom:22px;">
              Ciao!<br>
              Ecco le credenziali per accedere a <strong>Pasticcere Pro</strong>.<br>
              <strong>Indirizzo:</strong> <a href="https://gestionale.pasticcerepro.com" style="color:#e2ae76;">gestionale.pasticcerepro.com</a>
            </td>
          </tr>

          <tr>
            <td>
              <table  data-page-length="25"width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:10px;">
                <tr>
                  <td style="padding:16px 20px; font-size:15px; color:#111827;">
                    <div style="margin-bottom:6px;"><strong>Nome utente:</strong> {{ $email }}</div>
                    <div><strong>Password:</strong> {{ $password }}</div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:28px 0 10px 0;">
              <a href="https://gestionale.pasticcerepro.com" target="_blank"
                 style="background-color:#e2ae76; color:#041930; text-decoration:none; padding:14px 36px; border-radius:30px; font-weight:700; font-size:16px; display:inline-block; box-shadow:0 6px 16px rgba(226,174,118,0.45);">
                Accedi ora
              </a>
            </td>
          </tr>

          <tr>
            <td style="font-size:14px; color:#6b7280; line-height:1.6; padding-top:18px;">
              Puoi cambiare la password in qualsiasi momento in fase di login, cliccando su <strong>"Password dimenticata"</strong>.
            </td>
          </tr>

          <tr>
            <td style="font-size:16px; color:#33475b; line-height:1.6; padding:28px 0 6px;">
              Buon lavoro,<br>
              <strong>Il team di Pasticcere Pro</strong>
            </td>
          </tr>

          <tr>
            <td style="font-size:12px; color:#9ca3af; line-height:1.4; border-top:1px solid #e5e7eb; padding-top:18px;">
              Hai bisogno di aiuto? Scrivici a
              <a href="mailto:{{ $support }}" style="color:#e2ae76;">{{ $support }}</a>.
            </td>
          </tr>
        </table>
        <p style="font-size:12px; color:#9ca3af; margin-top:16px;">© {{ date('Y') }} Pasticcere Pro. Tutti i diritti riservati.</p>
      </td>
    </tr>
  </table>
</body>
</html>
