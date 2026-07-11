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
    td { padding: 8px 10px; font-size: 11px; border-bottom: 1px solid #f3f4f6; }
    .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; }
    .badge.actif      { background: #f0fdf4; color: #007A4D; }
    .badge.en_attente { background: #fefce8; color: #a16207; }
    .badge.suspendu   { background: #fef2f2; color: #CE1126; }
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
    <div class="section-title">Liste des fabricants ({{ $fabricants->count() }})</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Entreprise</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Ville</th>
                <th>Produits</th>
                <th>Statut</th>
                <th>Inscription</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fabricants as $i => $fab)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="font-weight:700;">{{ $fab->nom_entreprise }}</td>
                <td>{{ $fab->email }}</td>
                <td>{{ $fab->telephone ?? '—' }}</td>
                <td>{{ $fab->ville ?? '—' }}</td>
                <td style="font-weight:700;color:#2E3A6B;">{{ $fab->produits_count }}</td>
                <td><span class="badge {{ $fab->statut ?? 'en_attente' }}">{{ match($fab->statut ?? 'en_attente') { 'actif'=>'Actif','en_attente'=>'En attente','suspendu'=>'Suspendu',default=>ucfirst($fab->statut??'') } }}</span></td>
                <td>{{ $fab->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">VeriScan · Liste des fabricants · {{ now()->format('Y') }} · Document confidentiel</div>
</body>
</html>