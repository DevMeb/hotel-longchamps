<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 14px;
            color: #333;
        }
        .container {
            padding: 20px;
            margin: 0 auto;
            width: 700px;
        }
        .header {
            text-align: left;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header .address {
            font-size: 14px;
            line-height: 1.6;
        }
        .divider {
            border-top: 1px solid #000;
            margin: 20px 0;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .content {
            margin-bottom: 30px;
        }
        .content p {
            margin: 5px 0;
            line-height: 1.6;
        }
        .highlight {
            font-weight: bold;
        }
        .footer {
            text-align: left;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>LE LONGCHAMP SNC KENNEDY</h1>
            <p class="address">
                MR MEBARKI Hachemi<br>
                87 Avenue Marechal Foch<br>
                77500 CHELLES<br>
                Tél : 07.61.13.83.10
            </p>
        </div>

        <div class="divider"></div>

        <!-- Title -->
        <div class="section-title">Facture Réservation Hôtel</div>

        <!-- Content -->
        <div class="content">
            <p><strong>Locataire :</strong> {{ $invoice->reservation->renter->first_name }}</p>
            <p><strong>Objet :</strong> {{ $invoice->subject }}</p>
            <p>
                Je soussigné, <strong>MR MEBARKI</strong>, gérant du logement situé au 
                <strong>LE LONGCHAMP</strong>, 87 Avenue Marechal Foch, 77500 CHELLES, 
                réserve une chambre à <strong>{{ $invoice->reservation->renter->first_name }}</strong>, 
                pour la période du <strong>{{ \Carbon\Carbon::parse($invoice->billing_start_date)->format('d/m/Y') }}</strong> 
                au <strong>{{ \Carbon\Carbon::parse($invoice->billing_end_date)->format('d/m/Y') }}</strong>, 
                pour un montant de <strong>{{ number_format($invoice->reservation->room->rent / 100, 2, '.', ' ') }} €</strong>.
            </p>
            <p><strong>Chelles le :</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Paiement des réceptions</strong></p>
            <p>RIB en PJ</p>
            <p><strong>MR MEBARKI</strong> | 07 61 13 83 10</p>
        </div>
    </div>
</body>
</html>
