<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1F2937; }
    .header { background: linear-gradient(135deg, #171B3D, #2E3A6B); color: white; padding: 24px 28px; margin-bottom: 20px; }
    .logo { font-size: 20px; font-weight: 800; }
    .logo span { color: #F5A623; }
    .header h1 { font-size: 14px; font-weight: 700; margin-top: 8px; }
    .header-date { font-size: 10px; color: rgba(255,255,255,0.7); }
    .section { margin: 0 24px 20px; }
    .section-title { font-size: 12px; font-weight: 800; color: #2E3A6B; border-bottom: 2px solid #2E3A6B; padding-bottom: 5px; margin-bottom: 12px; text-transform: uppercase; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #EEF0F8; padding: 8px 10px; text-align: left; font-size: 9px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; }
    td { padding: 8px 10px; font-size: 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
    .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; }
    .badge.en_cours { background: #fefce8; color: #a16207; }
    .badge.traite   { background: #f0fdf4; color: #007A4D; }
    .badge.rejete   { background: #f9fafb; color: #6b7280; }
    .footer { margin: 24px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
    .empty { text-align: center; padding: 30px; color: #6b7280; }
</style>
</head>
<body>

<div class="header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div class="logo">Veri<span>Scan</span></div>
        <div class="header-date">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>
    <h1>Rapport des signalements — {{ $fabricant->nom_entreprise }}</h1>
</div>

<div class="section">
    <div class="section-title">Signalements reçus ({{ $signalements->count() }})</div>
    @if($signalements->count() > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Produit</th>
                <th>N° Lot</th>
                <th>Signalant</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($signalements as $sig)
            <tr>
                <td>#{{ $sig->id }}</td>
                <td style="font-weight:700;">{{ $sig->qrCode->lot->produit->nom ?? '—' }}</td>
                <td style="font-family:monospace;font-size:9px;">{{ $sig->qrCode->lot->numero_lot ?? '—' }}</td>
                <td>{{ $sig->nom_signalant ?? 'Anonyme' }}</td>
                <td style="max-width:180px;">{{ \Illuminate\Support\Str::limit($sig->description, 60) }}</td>
                <td><span class="badge {{ $sig->statut }}">{{ match($sig->statut) { 'en_cours'=>'En cours','traite'=>'Traité','rejete'=>'Rejeté',default=>$sig->statut } }}</span></td>
                <td style="white-space:nowrap;">{{ $sig->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty">Aucun signalement reçu</div>
    @endif
</div>

<div class="footer">
    VeriScan · Rapport signalements · {{ $fabricant->nom_entreprise }} · {{ now()->format('Y') }} · Document confidentiel
</div>
</body>
</html>