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
            position: relative;
            height: 100vh;
        }

        .container {
            padding: 20px;
            margin: 0 auto;
            width: 700px;
            position: relative;
            min-height: 100vh;
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

        .footer {
            text-align: left;
            margin-top: 30px;
        }

        /* ✅ Tampon centré en bas de la page */
        .stamp-container {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            width: 100%;
        }

        .stamp-container img {
            width: 200px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>LE LONGCHAMP</h1>
            <h2>SNC KENNEDY</h2>
            <p class="address">
                MR MEBARKI Gérant<br>
                87 Avenue Marechal Foch<br>
                77500 CHELLES<br>
                Tél: 07.61.13.83.10<br>
                Email: contact@hotel-longchamps.fr
            </p>
        </div>

        <div class="divider"></div>

        <!-- Title -->
        <div class="section-title">Facture Réservation Hôtel</div>

        <div class="divider"></div>

        <p style="text-align: center">
            <strong>
                {{ $invoice->subject }} 
                du {{ \Carbon\Carbon::parse($invoice->billing_start_date)->format('d/m/Y') }} 
                au {{ \Carbon\Carbon::parse($invoice->billing_end_date)->format('d/m/Y') }}
            </strong>
        </p>

        <!-- Content -->
        <div class="content">
            <p><strong>Locataire : {{ $invoice->reservation->renter->first_name . ' ' . $invoice->reservation->renter->last_name }}</strong></p>
            <p style="padding-left: 15px">{{ $invoice->description }}</p>
            <p><strong>Chelles le :</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Paiement des réceptions</strong></p>
            <p>RIB en PJ</p>
            <p><strong>MR MEBARKI Gérant</strong></p>
        </div>

        <!-- ✅ Tampon CENTRÉ en BAS de la page -->
        <div class="stamp-container">
            <img src="{{ $tamponPath }}" alt="Tampon">
        </div>
    </div>
</body>
</html>
