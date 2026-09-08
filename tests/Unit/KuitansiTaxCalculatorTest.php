<?php

namespace Tests\Unit;

use App\Services\KuitansiTaxCalculator;
use PHPUnit\Framework\TestCase;

class KuitansiTaxCalculatorTest extends TestCase
{
    public function test_compute_dpp_returns_zero_for_empty_or_null_input(): void
    {
        $this->assertSame(
            ['dpp' => 0, 'dpp_barang' => 0, 'dpp_jasa' => 0],
            KuitansiTaxCalculator::computeDpp(null)
        );

        $this->assertSame(
            ['dpp' => 0, 'dpp_barang' => 0, 'dpp_jasa' => 0],
            KuitansiTaxCalculator::computeDpp([])
        );
    }

    public function test_compute_dpp_separates_barang_and_jasa(): void
    {
        $rincian = [
            ['jumlah' => 2, 'harga_satuan' => 100000, 'is_jasa' => false],
            ['jumlah' => 1, 'harga_satuan' => 500000, 'is_jasa' => true],
        ];

        $result = KuitansiTaxCalculator::computeDpp($rincian);

        $this->assertSame(200000, $result['dpp_barang']);
        $this->assertSame(500000, $result['dpp_jasa']);
        $this->assertSame(700000, $result['dpp']);
    }

    public function test_compute_dpp_defaults_missing_or_invalid_jumlah_to_one_by_default(): void
    {
        $rincian = [
            ['harga_satuan' => 50000, 'is_jasa' => false], // no 'jumlah' key
            ['jumlah' => 0, 'harga_satuan' => 30000, 'is_jasa' => false], // zero jumlah
            ['jumlah' => -5, 'harga_satuan' => 10000, 'is_jasa' => false], // negative jumlah
            ['jumlah' => 'abc', 'harga_satuan' => 20000, 'is_jasa' => false], // non-numeric
        ];

        $result = KuitansiTaxCalculator::computeDpp($rincian);

        // Each invalid item falls back to 1 unit: 50000+30000+10000+20000
        $this->assertSame(110000, $result['dpp_barang']);
    }

    public function test_compute_dpp_can_use_a_custom_invalid_jumlah_default(): void
    {
        $rincian = [
            ['harga_satuan' => 50000, 'is_jasa' => false], // no 'jumlah' key
        ];

        $result = KuitansiTaxCalculator::computeDpp($rincian, 0);

        $this->assertSame(0, $result['dpp_barang']);
    }

    public function test_ppn_is_only_applied_when_checked(): void
    {
        $withPpn = KuitansiTaxCalculator::calculateFromDpp(1000000, 1000000, 0, true, 0, 0);
        $withoutPpn = KuitansiTaxCalculator::calculateFromDpp(1000000, 1000000, 0, false, 0, 0);

        $this->assertSame(110000, $withPpn['ppn']);
        $this->assertSame(0, $withoutPpn['ppn']);
    }

    public function test_pph22_only_applies_above_the_two_million_threshold_on_dpp_barang(): void
    {
        $atThreshold = KuitansiTaxCalculator::calculateFromDpp(2000000, 2000000, 0, false, 2, 0);
        $aboveThreshold = KuitansiTaxCalculator::calculateFromDpp(2000001, 2000001, 0, false, 2, 0);

        $this->assertSame(0, $atThreshold['pph_22'], 'exactly at the threshold should not trigger PPH 22');
        $this->assertSame(40000, $aboveThreshold['pph_22']);
        $this->assertSame('22', $aboveThreshold['jenis_pph']);
    }

    public function test_pph23_has_no_threshold_and_only_uses_dpp_jasa(): void
    {
        $result = KuitansiTaxCalculator::calculateFromDpp(500000, 0, 500000, false, 0, 1.5);

        $this->assertSame(7500, $result['pph_23']);
        $this->assertSame('23', $result['jenis_pph']);
    }

    public function test_jenis_pph_reports_both_when_pph22_and_pph23_both_apply(): void
    {
        $result = KuitansiTaxCalculator::calculateFromDpp(3000000, 3000000, 1000000, false, 2, 1.5);

        $this->assertGreaterThan(0, $result['pph_22']);
        $this->assertGreaterThan(0, $result['pph_23']);
        $this->assertSame('22,23', $result['jenis_pph']);
        $this->assertSame($result['pph_22'] + $result['pph_23'], $result['pph']);
    }

    public function test_jenis_pph_is_empty_when_no_tax_applies(): void
    {
        $result = KuitansiTaxCalculator::calculateFromDpp(1000000, 1000000, 0, false, 0, 0);

        $this->assertSame(0, $result['pph']);
        $this->assertSame('', $result['jenis_pph']);
    }

    public function test_calculate_combines_dpp_summation_and_rate_application(): void
    {
        $rincian = [
            ['jumlah' => 1, 'harga_satuan' => 3000000, 'is_jasa' => false],
        ];

        $result = KuitansiTaxCalculator::calculate($rincian, true, 2, 0);

        $this->assertSame(3000000, $result['dpp']);
        $this->assertSame(330000, $result['ppn']); // 11% of 3,000,000
        $this->assertSame(60000, $result['pph_22']); // 2% of 3,000,000 (above threshold)
        $this->assertSame('22', $result['jenis_pph']);
    }
}
