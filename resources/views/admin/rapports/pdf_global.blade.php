<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1F2937; background: white; }

    .header { background: linear-gradient(135deg, #042f2e, #0f766e); color: white; padding: 28px 32px; margin-bottom: 24px; }
    .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
    .logo { font-size: 22px; font-weight: 800; color: white; }
    .logo span { color: #5eead4; }
    .header-date { font-size: 11px; color: rgba(255,255,255,0.7); text-align: right; }
    .header h1 { font-size: 16px; font-weight: 700; margin-top: 12px; color: rgba(255,255,255,0.9); }

    .section { margin: 0 32px 24px; }
    .section-title { font-size: 13px; font-weight: 800; color: #0f766e; border-bottom: 2px solid #0f766e; padding-bottom: 6px; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.05em; }

    .stats-grid { display: table; width: 100%; border-spacing: 10px; }
    .stats-row { display: table-row; }
    .stat-box { display: table-cell; width: 25%; background: #f0fdfa; border: 1.5px solid #ccfbf1; border-radius: 8px; padding: 14px; text-align: center; }
    .stat-value { font-size: 28px; font-weight: 800; color: #0f766e; }
    .stat-label { font-size: 10px; color: #6b7280; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.04em; }

    table { width: 100%; border-collapse: collapse; }
    th { background: #f0fdfa; padding: 9px 12px; text-align: left; font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb; }
    td { padding: 9px 12px; font-size: 11px; border-bottom: 1px solid #f3f4f6; }
    tr:hover td { background: #f9fafb; }

    .sig-row { display: table; width: 100%; margin-bottom: 8px; }
    .sig-item { display: inline-block; width: 30%; padding: 10px 14px; border-radius: 8px; text-align: center; margin-right: 3%; }
    .sig-item.en_cours { background: #fefce8; border: 1px solid #fde68a; }
    .sig-item.traite   { background: #f0fdf4; border: 1px solid #bbf7d0; }
    .sig-item.rejete   { background: #f9fafb; border: 1px solid #e5e7eb; }
    .sig-count { font-size: 22px; font-weight: 800; }
    .sig-count.en_cours { color: #a16207; }
    .sig-count.traite   { color: #007A4D; }
    .sig-count.rejete   { color: #6b7280; }
    .sig-label { font-size: 10px; color: #9ca3af; margin-top: 2px; }

    .footer { margin: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }

    .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; }
    .badge.actif      { background: #f0fdf4; color: #007A4D; }
    .badge.en_attente { background: #fefce8; color: #a16207; }
    .badge.suspendu   { background: #fef2f2; color: #CE1126; }
</style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div class="logo">Veri<span>Scan</span></div>
        <div class="header-date">
            Généré le {{ $date }}<br>
            Rapport confidentiel
        </div>
    </div>
    <h1>{{ $titre }}</h1>
</div>

<div class="section">
    <div class="section-title">Vue d'ensemble de la plateforme</div>
    <table>
        <tr>
            <td style="width:25%;padding:10px;">
                <div class="stat-box" style="display:block;background:#f0fdfa;border:1.5px solid #ccfbf1;border-radius:8px;padding:14px;text-align:center;">
                    <div class="stat-value">{{ $fabricants }}</div>
                    <div class="stat-label">Fabricants</div>
                </div>
            </td>
            <td style="width:25%;padding:10px;">
                <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:28px;font-weight:800;color:#007A4D;">{{ $produits }}</div>
                    <div class="stat-label">Produits</div>
                </div>
            </td>
            <td style="width:25%;padding:10px;">
                <div style="background:#fefce8;border:1.5px solid #fde68a;border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:28px;font-weight:800;color:#a16207;">{{ number_format($scans) }}</div>
                    <div class="stat-label">Scans</div>
                </div>
            </td>
            <td style="width:25%;padding:10px;">
                <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:28px;font-weight:800;color:#CE1126;">{{ $signalements }}</div>
                    <div class="stat-label">Signalements</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Signalements par statut</div>
    <table>
        <tr>
            <td style="width:32%;padding:10px;">
                <div style="background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:12px;text-align:center;">
                    <div style="font-size:22px;font-weight:800;color:#a16207;">{{ $en_cours }}</div>
                    <div style="font-size:10px;color:#9ca3af;margin-top:2px;">En cours</div>
                </div>
            </td>
            <td style="width:32%;padding:10px;">
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px;text-align:center;">
                    <div style="font-size:22px;font-weight:800;color:#007A4D;">{{ $traites }}</div>
                    <div style="font-size:10px;color:#9ca3af;margin-top:2px;">Traités</div>
                </div>
            </td>
            <td style="width:32%;padding:10px;">
                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px;text-align:center;">
                    <div style="font-size:22px;font-weight:800;color:#6b7280;">{{ $rejetes }}</div>
                    <div style="font-size:10px;color:#9ca3af;margin-top:2px;">Rejetés</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Top fabricants par nombre de produits</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fabricant</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Produits</th>
                <th>Inscription</th>
            </tr>
        </thead>
        <tbody>
            @foreach($top_fabricants as $i => $fab)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="font-weight:700;">{{ $fab->nom_entreprise }}</td>
                <td>{{ $fab->email }}</td>
                <td><span class="badge {{ $fab->statut ?? 'en_attente' }}">{{ ucfirst($fab->statut ?? 'en_attente') }}</span></td>
                <td style="font-weight:700;color:#0f766e;">{{ $fab->produits_count }}</td>
                <td>{{ $fab->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    VeriScan — Plateforme anti-contrefaçon · Cameroun · {{ now()->format('Y') }} · Document confidentiel
</div>

</body>
</html>