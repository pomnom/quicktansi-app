// Handle kode_objek_pajak change - fetch tarif
$('#kode_objek_pajak, #edit_kode_objek_pajak').on('change', function() {
    const kode = $(this).val().trim();
    const iEdit = $(this).attr('id').startsWith('edit_');
    const tarifField = iEdit ? '#edit_tarif_pajak' : '#tarif_pajak';
    
    if (kode) {
        fetch(`/api/tarif-pajak/${kode}`)
            .then(response => {
                if (!response.ok) throw new Error('Kode pajak tidak ditemukan');
                return response.json();
            })
            .then(data => {
                $(tarifField).val(data.tarif);
                // Trigger PPH recalculation
                if (iEdit) {
                    calculateEditPPH();
                } else {
                    calculatePPH();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $(tarifField).val('');
                alert('Kode pajak tidak ditemukan');
                // Reset PPH display
                if (iEdit) {
                    document.getElementById('edit_pph_nominal').value = '0';
                    calculateEditTotalAkhir();
                } else {
                    document.getElementById('pph_nominal').value = '0';
                    calculateTotalAkhir();
                }
            });
    } else {
        $(tarifField).val('');
        // Reset PPH display
        if (iEdit) {
            document.getElementById('edit_pph_nominal').value = '0';
            calculateEditTotalAkhir();
        } else {
            document.getElementById('pph_nominal').value = '0';
            calculateTotalAkhir();
        }
    }
});

// Calculate DPP when items are added/removed (for display purposes only)
$(document).on('change', '.item-qty, .item-price', function() {
    calculatePPH();
    calculateTotalAkhir();
    calculateEditPPH();
    calculateEditTotalAkhir();
});

// Also recalculate when jenis_pph changes (for PPH 22 threshold)
$('#jenis_pph, #edit_jenis_pph').on('change', function() {
    const iEdit = $(this).attr('id').startsWith('edit_');
    if (iEdit) {
        calculateEditPPH();
        calculateEditTotalAkhir();
    } else {
        calculatePPH();
        calculateTotalAkhir();
    }
});

// Recalculate when PPN checkbox changes
$('#ppn_checkbox, #edit_ppn_checkbox').on('change', function() {
    const iEdit = $(this).attr('id').startsWith('edit_');
    if (iEdit) {
        calculateEditPPN();
        calculateEditTotalAkhir();
    } else {
        calculatePPN();
        calculateTotalAkhir();
    }
});

function calculateDPP() {
    // Calculate DPP from items
    let dpp = 0;
    document.querySelectorAll('#itemsBody tr').forEach(function(row) {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        dpp += qty * price;
    });
    return parseInt(dpp);
}

function calculateEditDPP() {
    // Calculate DPP from items
    let dpp = 0;
    document.querySelectorAll('#editItemsBody tr').forEach(function(row) {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        dpp += qty * price;
    });
    return parseInt(dpp);
}

function calculatePPN() {
    const dpp = calculateDPP();
    const ppnCheckbox = document.getElementById('ppn_checkbox');
    
    let ppnNominal = 0;
    if (ppnCheckbox && ppnCheckbox.checked) {
        ppnNominal = Math.round(dpp * 0.11); // PPN 11%
    }
    
    const ppnField = document.getElementById('ppn_nominal');
    if (ppnField) {
        ppnField.value = ppnNominal.toLocaleString('id-ID');
    }
    
    return ppnNominal;
}

function calculateEditPPN() {
    const dpp = calculateEditDPP();
    const ppnCheckbox = document.getElementById('edit_ppn_checkbox');
    
    let ppnNominal = 0;
    if (ppnCheckbox && ppnCheckbox.checked) {
        ppnNominal = Math.round(dpp * 0.11); // PPN 11%
    }
    
    const ppnField = document.getElementById('edit_ppn_nominal');
    if (ppnField) {
        ppnField.value = ppnNominal.toLocaleString('id-ID');
    }
    
    return ppnNominal;
}

function calculatePPH() {
    const dpp = calculateDPP();
    const tarif = parseFloat(document.getElementById('tarif_pajak').value) || 0;
    const jenisPph = document.getElementById('jenis_pph').value || '';
    
    // Calculate PPH with PPH 22 threshold logic
    let pphNominal = 0;
    if (tarif > 0) {
        // PPH 22: hanya jika belanja > 2.000.000
        // PPH 23: berlaku untuk semua belanja
        if (jenisPph === '22' && dpp <= 2000000) {
            pphNominal = 0; // Tidak kena pajak
        } else {
            pphNominal = Math.round(dpp * tarif / 100);
        }
    }
    
    const pphField = document.getElementById('pph_nominal');
    if (pphField) {
        pphField.value = pphNominal.toLocaleString('id-ID');
    }
    
    return pphNominal;
}

function calculateEditPPH() {
    const dpp = calculateEditDPP();
    const tarif = parseFloat(document.getElementById('edit_tarif_pajak').value) || 0;
    const jenisPph = document.getElementById('edit_jenis_pph').value || '';
    
    // Calculate PPH with PPH 22 threshold logic
    let pphNominal = 0;
    if (tarif > 0) {
        // PPH 22: hanya jika belanja > 2.000.000
        // PPH 23: berlaku untuk semua belanja
        if (jenisPph === '22' && dpp <= 2000000) {
            pphNominal = 0; // Tidak kena pajak
        } else {
            pphNominal = Math.round(dpp * tarif / 100);
        }
    }
    
    const pphField = document.getElementById('edit_pph_nominal');
    if (pphField) {
        pphField.value = pphNominal.toLocaleString('id-ID');
    }
    
    return pphNominal;
}

function calculateTotalAkhir() {
    const dppDisplay = document.getElementById('dpp_display');
    const dpp = calculateDPP();
    const ppn = calculatePPN();
    const pph = calculatePPH();
    
    // Update DPP display
    if (dppDisplay) {
        dppDisplay.value = 'Rp ' + dpp.toLocaleString('id-ID');
    }
    
    // Total Akhir = DPP + PPN - PPH
    const totalAkhir = dpp + ppn - pph;
    
    const totalField = document.getElementById('total_akhir_display');
    if (totalField) {
        totalField.value = 'Rp ' + totalAkhir.toLocaleString('id-ID');
    }
    
    return totalAkhir;
}

function calculateEditTotalAkhir() {
    const dppDisplay = document.getElementById('edit_dpp_display');
    const dpp = calculateEditDPP();
    const ppn = calculateEditPPN();
    const pph = calculateEditPPH();
    
    // Update DPP display
    if (dppDisplay) {
        dppDisplay.value = 'Rp ' + dpp.toLocaleString('id-ID');
    }
    
    // Total Akhir = DPP + PPN - PPH
    const totalAkhir = dpp + ppn - pph;
    
    const totalField = document.getElementById('edit_total_akhir_display');
    if (totalField) {
        totalField.value = 'Rp ' + totalAkhir.toLocaleString('id-ID');
    }
    
    return totalAkhir;
}