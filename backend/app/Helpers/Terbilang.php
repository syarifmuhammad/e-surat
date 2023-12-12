<?php
if (!function_exists('terbilang')) {
    function terbilang($angka)
    {
        $nilai = abs($angka);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = terbilang($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = terbilang($nilai / 10) . " puluh" . terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = terbilang($nilai / 100) . " ratus" . terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = terbilang($nilai / 1000) . " ribu" . terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = terbilang($nilai / 1000000) . " juta" . terbilang($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = terbilang($nilai / 1000000000) . " milyar" . terbilang(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = terbilang($nilai / 1000000000000) . " trilyun" . terbilang(fmod($nilai, 1000000000000));
        }
        return $temp;
    }
}
