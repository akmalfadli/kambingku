<?php

namespace App\Http\Controllers;

use App\Models\Goat;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function show(Goat $goat)
    {
        $url = url("/admin/goats/{$goat->id}");

        $qrCode = QrCode::size(200)
            ->format('png')
            ->generate($url);

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "inline; filename=\"goat-{$goat->tag_number}-qr.png\"");
    }
}
