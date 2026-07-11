<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1F2937; }
    .header { background: linear-gradient(135deg, #171B3D, #2E3A6B); color: white; padding: 24px 28px; margin-bottom: 20px; }
    .logo { font-size: 20px; font-weight: 800; color: white; }
    .logo span { color: #8B93D1; }
    .header-date { font-size: 10px; color: rgba(255,255,255,0.7); }
    .header h1 { font-size: 14px; font-weight: 700; margin-top: 8px; }
    .section { margin: 0 24px 20px; }
    .section-title { font-size: 12px; font-weight: 800; color: #2E3A6B; border-bottom: 2px solid #2E3A6B; padding-bottom: 5px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #EEF0F8; padding: 8px 10px; text-align: left; font-size: 9px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; }
    td { padding: 8px 10px; font-size: 10px; border-bottom: 1px solid #f3f4f6; }
    .footer { margin: 24px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
</style>
</head>
<body>
<div class="header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div class="logo">Veri<span>Scan</span></div>
        <div class="header-date">Généré le {{ $date }}</div>
    </div>
    <h1>{{ $titre }}</h1>
</div>

<div class="section">
    <div class="section-title">QR Codes par produit et lot</div>
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Fabricant</th>
                <th>Catégorie</th>
                <th>N° Lot</th>
                <th>Date fab.</th>
                <th>Date exp.</th>
                <th>QR générés</th>
                <th>Nb scans</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produits as $produit)
                @foreach($produit->lots as $lot)
                <tr>
                    <td style="font-weight:700;">{{ $produit->nom }}</td>
                    <td>{{ $produit->fabricant->nom_entreprise ?? '—' }}</td>
                    <td>{{ $produit->categorie }}</td>
                    <td style="font-family:monospace;">{{ $lot->numero_lot }}</td>
                    <td>{{ $lot->date_fabrication }}</td>
                    <td>{{ $lot->date_expiration }}</td>
                    <td style="font-weight:700;color:#2E3A6B;text-align:center;">{{ $lot->qrCodes->count() }}</td>
                    <td style="font-weight:700;text-align:center;">{{ $lot->qrCodes->sum('nb_scans') }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">VeriScan · Rapport QR Codes & Lots · {{ now()->format('Y') }} · Document confidentiel</div>
</body>
</html>