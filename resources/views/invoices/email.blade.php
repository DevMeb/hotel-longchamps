<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #0056b3;
        }
        .content p {
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Facture #{{ $invoice->id }}</h1>
            <p>{{ $invoice->subject }}</p>
        </div>

        <div class="content">
            <p>Bonjour,</p>
            <p>Vous trouverez en pièce jointe la facture concernant la réservation suivante :</p>
            <ul>
                <li><strong>Locataire :</strong> {{ $invoice->reservation->renter->first_name }} {{ $invoice->reservation->renter->last_name }}</li>
                <li><strong>Période :</strong> du {{ \Carbon\Carbon::parse($invoice->billing_start_date)->format('d/m/Y') }}
                    au {{ \Carbon\Carbon::parse($invoice->billing_end_date)->format('d/m/Y') }}</li>
                <li><strong>Montant :</strong> {{ number_format($invoice->reservation->room->rent / 100, 2, '.', ' ') }} €</li>
            </ul>
            <p>Merci pour votre confiance.</p>
        </div>

        <div class="footer">
            <p>Cordialement,<br>L'équipe de gestion</p>
        </div>
    </div>
</body>
</html>
