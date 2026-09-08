<?php

namespace App\Services;

class KuitansiTaxCalculator
{
    public const PPN_RATE = 0.11;
    public const PPH22_THRESHOLD = 2000000;

    /**
     * Sum rincian_item into DPP total, DPP Barang (non-jasa), and DPP Jasa.
     *
     * @param int $invalidJumlahDefault Quantity to use when an item's 'jumlah' is missing
     *   or not a positive number. The web form treats this case as 1 unit; kept
     *   configurable so callers with different historical behavior aren't changed silently.
     */
    public static function computeDpp(?array $rincianItem, int $invalidJumlahDefault = 1): array
    {
        $dpp = 0;
        $dppBarang = 0;
        $dppJasa = 0;

        if (is_array($rincianItem)) {
            foreach ($rincianItem as $item) {
                $jumlahRaw = $item['jumlah'] ?? null;
                $jumlah = (is_numeric($jumlahRaw) && (float) $jumlahRaw > 0) ? (int) $jumlahRaw : $invalidJumlahDefault;
                $harga = (float) ($item['harga_satuan'] ?? 0);
                $subtotal = $jumlah * $harga;

                $dpp += $subtotal;
                if (!empty($item['is_jasa'])) {
                    $dppJasa += $subtotal;
                } else {
                    $dppBarang += $subtotal;
                }
            }
        }

        return [
            'dpp' => (int) round($dpp),
            'dpp_barang' => (int) round($dppBarang),
            'dpp_jasa' => (int) round($dppJasa),
        ];
    }

    /**
     * Apply PPN/PPH rates to already-computed DPP figures.
     *
     * PPN: 11% of DPP, only if $ppnChecked is true.
     * PPH 22: from DPP Barang only, only above the threshold (> Rp 2.000.000).
     * PPH 23: from DPP Jasa only, no threshold.
     */
    public static function calculateFromDpp(int $dpp, int $dppBarang, int $dppJasa, bool $ppnChecked, float $tarifPajak22, float $tarifPajak23): array
    {
        $ppnAmount = $ppnChecked ? (int) round($dpp * self::PPN_RATE) : 0;

        $pph22Amount = ($tarifPajak22 > 0 && $dppBarang > self::PPH22_THRESHOLD)
            ? (int) round($dppBarang * $tarifPajak22 / 100)
            : 0;

        $pph23Amount = ($tarifPajak23 > 0 && $dppJasa > 0)
            ? (int) round($dppJasa * $tarifPajak23 / 100)
            : 0;

        $pphAmount = $pph22Amount + $pph23Amount;

        if ($pph22Amount > 0 && $pph23Amount > 0) {
            $jenisPph = '22,23';
        } elseif ($pph22Amount > 0) {
            $jenisPph = '22';
        } elseif ($pph23Amount > 0) {
            $jenisPph = '23';
        } else {
            $jenisPph = '';
        }

        return [
            'dpp' => $dpp,
            'dpp_barang' => $dppBarang,
            'dpp_jasa' => $dppJasa,
            'ppn' => $ppnAmount,
            'pph_22' => $pph22Amount,
            'pph_23' => $pph23Amount,
            'pph' => $pphAmount,
            'jenis_pph' => $jenisPph,
        ];
    }

    /**
     * Convenience wrapper: sum rincian_item (web-form default of 1 unit for
     * missing/invalid quantities) and apply PPN/PPH rates in one call.
     */
    public static function calculate(?array $rincianItem, bool $ppnChecked, float $tarifPajak22, float $tarifPajak23): array
    {
        $dppResult = self::computeDpp($rincianItem);

        return self::calculateFromDpp(
            $dppResult['dpp'],
            $dppResult['dpp_barang'],
            $dppResult['dpp_jasa'],
            $ppnChecked,
            $tarifPajak22,
            $tarifPajak23
        );
    }
}
