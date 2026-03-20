<?php

namespace App\Helpers;

/**
 * ProgressChartGenerator
 *
 * Membuat grafik garis (line chart) sebagai string base64 PNG
 * menggunakan ekstensi GD (built-in PHP).
 *
 * Cara pakai di Controller:
 *
 *   use App\Helpers\ProgressChartGenerator;
 *
 *   $chartBase64 = ProgressChartGenerator::generate(
 *       $progressData['total'],   // array skor, misal [55, 62, 70, 68]
 *       $progressData['labels']   // array label, misal ['Jan', 'Feb', 'Mar', 'Apr']
 *   );
 *
 *   return view('pdf.laporan-progress', compact(
 *       'inmate', 'assessments', 'progressData',
 *       'startDate', 'endDate', 'chartBase64'
 *   ));
 *
 * Di Blade:
 *   <img src="data:image/png;base64,{{ $chartBase64 }}" style="width:100%;" />
 */
class ProgressChartGenerator
{
    // Ukuran kanvas
    const WIDTH  = 800;
    const HEIGHT = 280;

    // Padding area grafik
    const PAD_LEFT   = 55;
    const PAD_RIGHT  = 20;
    const PAD_TOP    = 25;
    const PAD_BOTTOM = 50;

    /**
     * @param  array  $scores  Array nilai (float/int), contoh [55, 62, 70]
     * @param  array  $labels  Array label periode,    contoh ['Jan', 'Feb', 'Mar']
     * @return string          Base64-encoded PNG
     */
    public static function generate(array $scores, array $labels): string
    {
        $scores = array_values($scores);
        $labels = array_values($labels);
        $n      = count($scores);

        $W  = self::WIDTH;
        $H  = self::HEIGHT;
        $pL = self::PAD_LEFT;
        $pR = self::PAD_RIGHT;
        $pT = self::PAD_TOP;
        $pB = self::PAD_BOTTOM;

        $cW = $W - $pL - $pR;   // lebar area chart
        $cH = $H - $pT - $pB;   // tinggi area chart

        // ---- Buat image ----
        $img = imagecreatetruecolor($W, $H);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        // ---- Warna ----
        $cWhite      = imagecolorallocate($img, 255, 255, 255);
        $cBgChart    = imagecolorallocate($img, 249, 249, 249);
        $cGrid       = imagecolorallocate($img, 204, 204, 204);
        $cAxis       = imagecolorallocate($img, 80,  80,  80);
        $cLine       = imagecolorallocate($img, 34,  34,  34);
        $cDot        = imagecolorallocate($img, 255, 255, 255);
        $cDotBorder  = imagecolorallocate($img, 34,  34,  34);
        $cValueText  = imagecolorallocate($img, 17,  17,  17);
        $cLabelText  = imagecolorallocate($img, 80,  80,  80);
        $cAreaFill   = imagecolorallocatealpha($img, 150, 150, 150, 95); // abu semi-transparan

        // ---- Background ----
        imagefilledrectangle($img, 0, 0, $W - 1, $H - 1, $cWhite);

        // ---- Background area chart ----
        imagefilledrectangle($img, $pL, $pT, $pL + $cW, $pT + $cH, $cBgChart);

        // ---- Grid horizontal & label Y (0,20,40,60,80,100) ----
        for ($g = 0; $g <= 5; $g++) {
            $val = $g * 20;
            $gy  = (int) round($pT + $cH - ($cH * $val / 100));

            // grid putus-putus
            for ($dx = 0; $dx < $cW; $dx += 6) {
                imageline($img, $pL + $dx, $gy, min($pL + $dx + 3, $pL + $cW), $gy, $cGrid);
            }

            // label Y
            $lbl = (string) $val;
            $lx  = $pL - (strlen($lbl) * 7) - 5;
            imagestring($img, 2, max(2, $lx), $gy - 7, $lbl, $cLabelText);
        }

        // ---- Hitung koordinat titik ----
        $xStep = $n > 1 ? $cW / ($n - 1) : $cW / 2;
        $pts   = [];
        foreach ($scores as $i => $s) {
            $pts[] = [
                'px' => (int) round($pL + $xStep * $i),
                'py' => (int) round($pT + $cH - ($cH * (min(max($s, 0), 100) / 100))),
                's'  => $s,
                'l'  => $labels[$i] ?? '',
            ];
        }

        // ---- Area fill (polygon di bawah garis) ----
        if ($n >= 2) {
            // Buat polygon: titik kiri-bawah, semua pts, titik kanan-bawah
            $polyPts = [];
            $polyPts[] = $pts[0]['px'];
            $polyPts[] = $pT + $cH;
            foreach ($pts as $p) {
                $polyPts[] = $p['px'];
                $polyPts[] = $p['py'];
            }
            $polyPts[] = $pts[$n - 1]['px'];
            $polyPts[] = $pT + $cH;
            imagefilledpolygon($img, $polyPts, $cAreaFill);
        }

        // ---- Garis penghubung (antialiased) ----
        imageantialias($img, true);
        if ($n >= 2) {
            for ($i = 0; $i < $n - 1; $i++) {
                imageline($img, $pts[$i]['px'], $pts[$i]['py'],
                                $pts[$i+1]['px'], $pts[$i+1]['py'], $cLine);
                // gambar 2x untuk ketebalan
                imageline($img, $pts[$i]['px'], $pts[$i]['py'] + 1,
                                $pts[$i+1]['px'], $pts[$i+1]['py'] + 1, $cLine);
            }
        }

        // ---- Titik data, nilai atas, label bawah ----
        foreach ($pts as $p) {
            // Garis vertikal bantu putus-putus
            for ($dy = $p['py'] + 6; $dy < $pT + $cH; $dy += 6) {
                imagesetpixel($img, $p['px'], $dy, $cGrid);
            }

            // Lingkaran titik (isi putih, border hitam)
            $r = 5;
            imagefilledellipse($img, $p['px'], $p['py'], $r * 2, $r * 2, $cDot);
            imagearc($img, $p['px'], $p['py'], $r * 2 + 2, $r * 2 + 2, 0, 360, $cDotBorder);
            imagearc($img, $p['px'], $p['py'], $r * 2 + 1, $r * 2 + 1, 0, 360, $cDotBorder);

            // Nilai di atas titik
            $valStr = number_format($p['s'], 0);
            $vx     = $p['px'] - (strlen($valStr) * 3);
            imagestring($img, 2, $vx, $p['py'] - 16, $valStr, $cValueText);

            // Label periode di bawah sumbu X
            $llen = strlen($p['l']);
            $lx   = $p['px'] - (int)($llen * 3.5);
            imagestring($img, 1, $lx, $pT + $cH + 8, $p['l'], $cLabelText);
        }

        // ---- Sumbu X dan Y ----
        imageline($img, $pL, $pT, $pL, $pT + $cH, $cAxis);           // sumbu Y
        imageline($img, $pL, $pT + $cH, $pL + $cW, $pT + $cH, $cAxis); // sumbu X
        // Tebalkan (gambar 1px di sebelahnya)
        imageline($img, $pL + 1, $pT, $pL + 1, $pT + $cH, $cAxis);
        imageline($img, $pL, $pT + $cH + 1, $pL + $cW, $pT + $cH + 1, $cAxis);

        // ---- Border area chart ----
        imagerectangle($img, $pL, $pT, $pL + $cW, $pT + $cH, $cGrid);

        // ---- Output ke base64 ----
        ob_start();
        imagepng($img);
        $raw = ob_get_clean();
        imagedestroy($img);

        return base64_encode($raw);
    }
}
