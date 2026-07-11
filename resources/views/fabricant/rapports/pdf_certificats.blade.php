<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1F2937; }
    .header { background: linear-gradient(135deg, #171B3D, #2E3A6B); color: white; padding: 24px 28px; margin-bottom: 20px; }
    .logo { font-size: 20px; font-weight: 800; color: white; }
    .logo span { color: #F5A623; }
    .header h1 { font-size: 14px; font-weight: 700; margin-top: 8px; }
    .header-date { font-size: 10px; color: rgba(255,255,255,0.7); }
    .section { margin: 0 24px 24px; }
    .section-title { font-size: 12px; font-weight: 800; color: #2E3A6B; border-bottom: 2px solid #2E3A6B; padding-bottom: 5px; margin-bottom: 14px; text-transform: uppercase; }

    .cert-box {
        border: 2px solid #2E3A6B; border-radius: 8px;
        padding: 16px 20px; margin-bottom: 16px;
        page-break-inside: avoid;
    }
    .cert-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
    .cert-title { font-size: 13px; font-weight: 800; color: #2E3A6B; }
    .cert-badge { background: #f0fdf4; color: #007A4D; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 12px; border: 1px solid #bbf7d0; }
    .cert-grid { display: table; width: 100%; }
    .cert-row { display: table-row; }
    .cert-label { display: table-cell; width: 35%; font-size: 10px; color: #6b7280; padding: 3px 0; }
    .cert-value { display: table-cell; font-size: 11px; font-weight: 600; color: #1F2937; padding: 3px 0; }
    .cert-lot { background: #EEF0F8; border-radius: 6px; padding: 8px 12px; margin-top: 10px; }
    .cert-lot-title { font-size: 10px; font-weight: 700; color: #2E3A6B; margin-bottom: 6px; text-transform: uppercase; }
    table { width: 100%; border-collapse: collapse; }
    th { font-size: 9px; font-weight: 700; color: #6b7280; text-transform: uppercase; padding: 5px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    td { font-size: 10px; padding: 5px 8px; border-bottom: 1px solid #f3f4f6; }

    .seal { text-align: center; margin: 20px 24px; padding: 14px; border: 1.5px dashed #2E3A6B; border-radius: 8px; background: #EEF0F8; }
    .seal-text { font-size: 11px; font-weight: 700; color: #2E3A6B; }
    .seal-sub { font-size: 10px; color: #6b7280; margin-top: 3px; }
    .footer { margin: 24px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
</style>
</head>
<body>

<div class="header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div class="logo">Veri<span>Scan</span></div>
        <div class="header-date">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>
    <h1>Certificats d'authenticité — {{ $fabricant->nom_entreprise }}</h1>
</div>

<div class="seal">
    <div class="seal-text">✓ CERTIFIÉ AUTHENTIQUE PAR VERISCAN</div>
    <div class="seal-sub">Plateforme anti-contrefaçon · Cameroun · {{ now()->format('Y') }}</div>
</div>

@forelse($produits as $produit)
<div class="section">
    <div class="cert-box">
        <div class="cert-header">
            <div class="cert-title">{{ $produit->nom }}</div>
            <div class="cert-badge">✓ Authentique</div>
        </div>
        <div class="cert-grid">
            <div class="cert-row">
                <div class="cert-label">Fabricant</div>
                <div class="cert-value">{{ $fabricant->nom_entreprise }}</div>
            </div>
            <div class="cert-row">
                <div class="cert-label">Catégorie</div>
                <div class="cert-value">{{ $produit->categorie }}</div>
            </div>
            <div class="cert-row">
                <div class="cert-label">Code produit</div>
                <div class="cert-value" style="font-family:monospace;">{{ $produit->code_produit }}</div>
            </div>
            <div class="cert-row">
                <div class="cert-label">Date d'enregistrement</div>
                <div class="cert-value">{{ $produit->created_at->format('d/m/Y') }}</div>
            </div>
        </div>

        @if($produit->lots->count() > 0)
        <div class="cert-lot">
            <div class="cert-lot-title">Lots certifiés ({{ $produit->lots->count() }})</div>
            <table>
                <thead>
                    <tr>
                        <th>N° Lot</th>
                        <th>Date fabrication</th>
                        <th>Date expiration</th>
                        <th>QR générés</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produit->lots as $lot)
                    <tr>
                        <td style="font-weight:700;font-family:monospace;">{{ $lot->numero_lot }}</td>
                        <td>{{ $lot->date_fabrication }}</td>
                        <td>{{ $lot->date_expiration }}</td>
                        <td style="text-align:center;font-weight:700;color:#2E3A6B;">{{ $lot->qrCodes->count() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@empty
<div class="section" style="text-align:center;color:#6b7280;padding:30px;">
    Aucun produit enregistré
</div>
@endforelse

<div class="footer">
    VeriScan · Certificats d'authenticité · {{ $fabricant->nom_entreprise }} · {{ now()->format('Y') }} · Document officiel
</div>
</body>
</html>