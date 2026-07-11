<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<title>{{ __('messages.rapport_mensuel') }} VeriScan — {{ $date->format('F Y') }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 13px; color: #1f2937; background: #fff; }
    .header { background: #2E3A6B; color: #fff; padding: 24px 32px; margin-bottom: 28px; }
    .header h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
    .header p { font-size: 13px; opacity: .85; }
    .header .meta { margin-top: 12px; font-size: 12px; opacity: .75; }
    .section { padding: 0 32px; margin-bottom: 28px; }
    .section-title { font-size: 15px; font-weight: 700; color: #2E3A6B; border-bottom: 2px solid #2E3A6B; padding-bottom: 6px; margin-bottom: 16px; }
    .stats-row { display: flex; gap: 16px; margin-bottom: 24px; }
    .stat-box { flex: 1; background: #EEF0F8; border: 1px solid #c7cce6; border-radius: 8px; padding: 16px; text-align: center; }
    .stat-value { font-size: 26px; font-weight: 700; color: #2E3A6B; }
    .stat-label { font-size: 11px; color: #6b7280; margin-top: 4px; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th { background: #EEF0F8; padding: 8px 12px; text-align: left; font-weight: 600; color: #2E3A6B; border: 1px solid #e5e7eb; }
    td { padding: 8px 12px; border: 1px solid #e5e7eb; vertical-align: top; }
    tr:nth-child(even) td { background: #f9fafb; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; }
    .badge-green { background: #dcfce7; color: #16a34a; }
    .badge-yellow { background: #fef9c3; color: #ca8a04; }
    .badge-red { background: #fee2e2; color: #dc2626; }
    .footer { margin-top: 40px; padding: 16px 32px; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; display: flex; justify-content: space-between; }
    .no-data { text-align: center; color: #9ca3af; padding: 20px; font-style: italic; }
    .page-break { page-break-after: always; }
</style>
</head>
<body>

{{-- En-tête --}}
<div class="header">
    <h1>{{ __('messages.rapport_mensuel') }} VeriScan</h1>
    <p>{{ $date->format('F Y') }}</p>
    <div class="meta">
        {{ __('messages.fabricant_label') }} : {{ $fabricant->nom_entreprise ?? $fabricant->name }} |
        {{ __('messages.genere_le') }} {{ now()->format('d/m/Y à H:i') }}
    </div>
</div>

{{-- Statistiques globales --}}
<div class="section">
    <div class="section-title">{{ __('messages.resume_du_mois') }}</div>
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-value">{{ $produits->count() }}</div>
            <div class="stat-label">{{ __('messages.produits_actifs') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $produits->sum(fn($p) => $p->lots->count()) }}</div>
            <div class="stat-label">{{ __('messages.lots_ce_mois') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $signalements->count() }}</div>
            <div class="stat-label">{{ __('messages.signalements_recus') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $signalements->where('statut','traite')->count() }}</div>
            <div class="stat-label">{{ __('messages.signalements_traites') }}</div>
        </div>
    </div>
</div>

{{-- Produits --}}
<div class="section">
    <div class="section-title">{{ __('messages.produits_enregistres') }}</div>
    @if($produits->isEmpty())
        <p class="no-data">{{ __('messages.aucun_produit_periode') }}</p>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('messages.nom_produit') }}</th>
                <th>{{ __('messages.categorie') }}</th>
                <th>{{ __('messages.code_produit') }}</th>
                <th>{{ __('messages.lots_ce_mois') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produits as $produit)
            <tr>
                <td>{{ $produit->nom }}</td>
                <td>{{ $produit->categorie }}</td>
                <td style="font-family:monospace;font-size:11px">{{ $produit->code_produit }}</td>
                <td>{{ $produit->lots->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Signalements --}}
<div class="section">
    <div class="section-title">{{ __('messages.signalements_recus_mois') }}</div>
    @if($signalements->isEmpty())
        <p class="no-data">{{ __('messages.aucun_signalement_periode') }}</p>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('messages.date') }}</th>
                <th>{{ __('messages.produit') }}</th>
                <th>{{ __('messages.numero_lot') }}</th>
                <th>{{ __('messages.signalant') }}</th>
                <th>{{ __('messages.statut') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($signalements as $s)
            <tr>
                <td>{{ $s->created_at->format('d/m/Y') }}</td>
                <td>{{ $s->qrCode->lot->produit->nom ?? '—' }}</td>
                <td>{{ $s->qrCode->lot->numero_lot ?? '—' }}</td>
                <td>{{ $s->nom_signalant ?? __('messages.anonyme') }}</td>
                <td>
                    @if($s->statut === 'en_cours')
                        <span class="badge badge-yellow">{{ __('messages.en_cours') }}</span>
                    @elseif($s->statut === 'traite')
                        <span class="badge badge-green">{{ __('messages.traite') }}</span>
                    @else
                        <span class="badge badge-red">{{ __('messages.rejete') }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

</body>
</html>