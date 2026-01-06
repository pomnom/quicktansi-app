<?php

namespace App\Helpers;

class NumberToWordHelper
{
    /**
     * Convert number to Indonesian words (Terbilang)
     * 
     * @param int $nilai
     * @return string
     */
    public static function terbilang($nilai)
    {
        $nilai = abs((int) $nilai);
        
        if ($nilai === 0) {
            return 'Nol';
        }
        
        return trim(self::penyebut($nilai));
    }
    
    /**
     * Helper function untuk konversi bilangan
     * 
     * @param int $nilai
     * @return string
     */
    private static function penyebut($nilai)
    {
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp = "";
        
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } elseif ($nilai < 20) {
            $temp = self::penyebut($nilai - 10) . " Belas";
        } elseif ($nilai < 100) {
            $temp = self::penyebut(intval($nilai / 10)) . " Puluh" . self::penyebut($nilai % 10);
        } elseif ($nilai < 200) {
            $temp = " Seratus" . self::penyebut($nilai - 100);
        } elseif ($nilai < 1000) {
            $temp = self::penyebut(intval($nilai / 100)) . " Ratus" . self::penyebut($nilai % 100);
        } elseif ($nilai < 2000) {
            $temp = " Seribu" . self::penyebut($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $temp = self::penyebut(intval($nilai / 1000)) . " Ribu" . self::penyebut($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            $temp = self::penyebut(intval($nilai / 1000000)) . " Juta" . self::penyebut($nilai % 1000000);
        } elseif ($nilai < 1000000000000) {
            $temp = self::penyebut(intval($nilai / 1000000000)) . " Milyar" . self::penyebut($nilai % 1000000000);
        } else {
            $temp = self::penyebut(intval($nilai / 1000000000000)) . " Triliun" . self::penyebut($nilai % 1000000000000);
        }
        
        return $temp;
    }
}
