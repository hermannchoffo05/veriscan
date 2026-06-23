<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Produit;
use App\Models\Lot;
use App\Models\QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrGen;

class FabricantQRCodesController extends Controller
{
    public function index()
    {
        $fabricant = Auth::guard('fabricant')->user();
        $qrcodes = QrCode::whereHas('lot.produit', function ($q) use ($fabricant) {
                        $q->where('fabricant_id', $fabricant->id);
                    })
                    ->with('lot.produit')
                    ->latest()
                    ->paginate(15);
        return view('fabricant.qrcodes.index', compact('qrcodes', 'fabricant'));
    }

    public function create()
    {
        $fabricant = Auth::guard('fabricant')->user();
        $lots = Lot::whereHas('produit', function ($q) use ($fabricant) {
                    $q->where('fabricant_id', $fabricant->id);
                })
                ->with('produit')
                ->latest()
                ->get();
        return view('fabricant.qrcodes.create', compact('lots', 'fabricant'));
    }

    public function store(Request $request)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $validated = $request->validate([
            'lot_id'   => 'required|exists:lots,id',
            'quantite' => 'required|integer|min:1|max:500',
        ]);

        $lot = Lot::whereHas('produit', function ($q) use ($fabricant) {
                    $q->where('fabricant_id', $fabricant->id);
                })->findOrFail($validated['lot_id']);

        $generated = 0;
        for ($i = 0; $i < $validated['quantite']; $i++) {
            $token = strtoupper('VS-' . $lot->id . '-' . Str::random(12));
            QrCode::create([
                'lot_id'   => $lot->id,
                'token'    => $token,
                'hmac'     => hash_hmac('sha256', $token, config('app.key')),
                'statut'   => 'actif',
                'nb_scans' => 0,
            ]);
            $generated++;
        }

        return redirect()->route('fabricant.qrcodes.index')
                         ->with('success', $generated . ' QR code(s) générés pour le lot ' . $lot->numero_lot . '.');
    }

    /**
     * Crée une image logo avec fond blanc circulaire pour l'insertion au centre du QR code.
     * Retourne le chemin vers le fichier temporaire créé.
     */
    private function buildLogoWithWhiteCircle(int $size = 80): string
    {
        $logoPath = public_path('images/logo.png');

        // Charger le logo original
        $src = imagecreatefrompng($logoPath);
        $srcW = imagesx($src);
        $srcH = imagesy($src);

        // Créer le canvas carré avec fond transparent
        $canvas = imagecreatetruecolor($size, $size);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        // Ombre portée (cercle gris décalé légèrement)
        imagealphablending($canvas, true);
        $padding   = (int)($size * 0.08);
        $shadowOff = (int)($size * 0.06); // décalage ombre
        $shadow    = imagecolorallocatealpha($canvas, 0, 0, 0, 90); // noir semi-transparent
        imagefilledellipse($canvas, $size / 2 + $shadowOff, $size / 2 + $shadowOff, $size - $padding, $size - $padding, $shadow);

        // Cercle blanc principal
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledellipse($canvas, $size / 2, $size / 2, $size - $padding, $size - $padding, $white);

        // Redimensionner et coller le logo au centre (75% du cercle)
        $logoSize = (int)($size * 0.62);
        $offset   = (int)(($size - $logoSize) / 2);
        imagecopyresampled($canvas, $src, $offset, $offset, 0, 0, $logoSize, $logoSize, $srcW, $srcH);

        // Sauvegarder dans un fichier temporaire
        $tmpPath = sys_get_temp_dir() . '/veriscan_logo_circle_' . $size . '.png';
        imagesavealpha($canvas, true);
        imagepng($canvas, $tmpPath);

        imagedestroy($src);
        imagedestroy($canvas);

        return $tmpPath;
    }

    public function show($id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $qrcode = QrCode::whereHas('lot.produit', function ($q) use ($fabricant) {
                        $q->where('fabricant_id', $fabricant->id);
                    })
                    ->with('lot.produit')
                    ->findOrFail($id);

        $verifyUrl = route('verify.token', $qrcode->token);

        // Logo avec cercle blanc (taille adaptée au QR 300px)
        $logoTmp = $this->buildLogoWithWhiteCircle(80);

        $qrImage = base64_encode(
            QrGen::format('png')
                ->size(300)
                ->margin(2)
                ->errorCorrection('H')
                ->merge($logoTmp, 0.28, true)
                ->generate($verifyUrl)
        );

        return view('fabricant.qrcodes.show', compact('qrcode', 'qrImage', 'fabricant'));
    }

    public function download($id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $qrcode = QrCode::whereHas('lot.produit', function ($q) use ($fabricant) {
                        $q->where('fabricant_id', $fabricant->id);
                    })->findOrFail($id);

        $verifyUrl = route('verify.token', $qrcode->token);

        // Logo avec cercle blanc (taille adaptée au QR 600px)
        $logoTmp = $this->buildLogoWithWhiteCircle(160);

        $image = QrGen::format('png')
            ->size(600)
            ->margin(2)
            ->errorCorrection('H')
            ->merge($logoTmp, 0.28, true)
            ->generate($verifyUrl);

        return response($image)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="veriscan-qr-' . $qrcode->token . '.png"');
    }
    public function downloadLotPdf($lotId)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $lot = \App\Models\Lot::whereHas('produit', function ($q) use ($fabricant) {
                    $q->where('fabricant_id', $fabricant->id);
                })->with(['produit', 'qrcodes'])->findOrFail($lotId);

        $qrcodes = $lot->qrcodes;
        $logoTmp = $this->buildLogoWithWhiteCircle(160);

        $etiquettes = '';
        foreach ($qrcodes as $qrcode) {
            $verifyUrl = route('verify.token', $qrcode->token);
            $qrImage = base64_encode(
                QrGen::format('png')
                    ->size(600)
                    ->margin(2)
                    ->errorCorrection('H')
                    ->merge($logoTmp, 0.28, true)
                    ->generate($verifyUrl)
            );
            $etiquettes .= '
                <div class="etiquette">
                    <img src="data:image/png;base64,' . $qrImage . '" alt="QR">
                    <div class="token">' . $qrcode->token . '</div>
                </div>';
        }

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: DejaVu Sans, sans-serif; background: #fff; }
                .grille { display: table; width: 100%; }
                .ligne { display: table-row; }
                .etiquette { display: table-cell; width: 113px; height: 120px; padding: 4px; text-align: center; vertical-align: middle; border: 0.5px dashed #e5e7eb; }
                .etiquette img { width: 96px; height: 96px; }
                .token { font-family: monospace; font-size: 7px; color: #4b5563; margin-top: 2px; }
            </style>
        </head>
        <body>
            <div class="grille">' . $etiquettes . '</div>
        </body>
        </html>';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('A4', 'portrait');

        return $pdf->download('veriscan-lot-' . $lot->numero_lot . '.pdf');
    }

    public function downloadPdf($id)
    {
        $fabricant = Auth::guard('fabricant')->user();
        $qrcode = QrCode::whereHas('lot.produit', function ($q) use ($fabricant) {
                        $q->where('fabricant_id', $fabricant->id);
                    })
                    ->with('lot.produit')
                    ->findOrFail($id);

        $verifyUrl = route('verify.token', $qrcode->token);
        $logoTmp   = $this->buildLogoWithWhiteCircle(160);

        $qrImage = base64_encode(
            QrGen::format('png')
                ->size(600)
                ->margin(2)
                ->errorCorrection('H')
                ->merge($logoTmp, 0.28, true)
                ->generate($verifyUrl)
        );

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                html, body { width: 100%; height: 100%; font-family: DejaVu Sans, sans-serif; background: #fff; }
                .outer { width: 100%; display: table; margin-top: 18px; }
                .middle { display: table-cell; vertical-align: middle; text-align: center; }
                .label { display: inline-block; text-align: center; }
                .label img { width: 96px; height: 96px; }
                .token { font-family: monospace; font-size: 8px; color: #4b5563; margin-top: 4px; }
            </style>
        </head>
        <body>
            <div class="outer">
                <div class="middle">
                    <div class="label">
                        <img src="data:image/png;base64,' . $qrImage . '" alt="QR Code">
                        <div class="token">' . $qrcode->token . '</div>
                    </div>
                </div>
            </div>
        </body>
        </html>';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper([0, 0, 113, 113], 'portrait');

        return $pdf->download('veriscan-qr-' . $qrcode->token . '.pdf');
    }
}