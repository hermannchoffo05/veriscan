@extends('layouts.fabricant')

@section('topbar-title', $locale === 'en' ? 'Subscription' : 'Abonnement')

@section('content')
<div style="max-width: 1100px; margin: 0 auto; padding: 8px 0 32px;">
    <div style="margin-bottom: 8px;">
        <h1 style="font-size: 22px; font-weight: 800; color: #1F2937; margin-bottom: 4px;">
            {{ $locale === 'en' ? 'Choose your plan' : 'Choisissez votre plan' }}
        </h1>
        <p style="font-size: 14px; color: #6b7280;">
            {{ $locale === 'en' ? 'Change your plan whenever you want. Effective right after payment.' : 'Changez de plan quand vous le souhaitez. Effectif immédiatement après paiement.' }}
        </p>
    </div>

    @include('tarifs._plans', ['locale' => $locale, 'fabricant' => $fabricant, 'planActif' => $planActif])
</div>
@endsection