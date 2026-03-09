// Handle kode_objek_pajak input - format display with full info
$(
    "#kode_objek_pajak_22, #edit_kode_objek_pajak_22, #kode_objek_pajak_23, #edit_kode_objek_pajak_23",
).on("input", function () {
    const input = $(this);
    const value = input.val().trim();
    const datalistId = input.attr("list");

    if (!value || !datalistId) return;

    // Find matching option in datalist
    const option = $(`#${datalistId} option[value="${value}"]`);

    if (option.length > 0) {
        // Get the full text from option (e.g., "24-101-01 - Dividen (15%)")
        const fullText = option.text();

        // Only update if different to avoid cursor jumping
        if (input.val() !== fullText) {
            input.val(fullText);
        }
    }
});

// Handle kode_objek_pajak change - fetch tarif for PPH 22 or PPH 23
$(
    "#kode_objek_pajak_22, #edit_kode_objek_pajak_22, #kode_objek_pajak_23, #edit_kode_objek_pajak_23",
).on("change", function () {
    const input = $(this);
    const inputId = input.attr("id");
    const fullValue = input.val().trim();

    // Extract just the code (everything before " -")
    const kode = fullValue.split(" -")[0].trim();

    const isEdit = inputId.startsWith("edit_");
    const isPph23 = inputId.endsWith("_23");
    const tarifField = isPph23
        ? isEdit
            ? "#edit_tarif_pajak_23"
            : "#tarif_pajak_23"
        : isEdit
          ? "#edit_tarif_pajak"
          : "#tarif_pajak";

    if (kode) {
        fetch(`/api/tarif-pajak/${kode}`)
            .then((response) => {
                if (!response.ok) throw new Error("Kode pajak tidak ditemukan");
                return response.json();
            })
            .then((data) => {
                $(tarifField).val(data.tarif);
                isEdit ? calculateEditTotalAkhir() : calculateTotalAkhir();
            })
            .catch((error) => {
                console.error("Error:", error);
                $(tarifField).val("");
                alert("Kode pajak tidak ditemukan");
                input.val(kode);
                isEdit ? calculateEditTotalAkhir() : calculateTotalAkhir();
            });
    } else {
        $(tarifField).val("");
        isEdit ? calculateEditTotalAkhir() : calculateTotalAkhir();
    }
});

// Calculate DPP when items are added/removed (for display purposes only)
$(document).on("change", ".item-qty, .item-price", function () {
    calculatePPH();
    calculateTotalAkhir();
    calculateEditPPH();
    calculateEditTotalAkhir();
});

// Recalculate PPH 23 when jasa checkbox changes
$(document).on("change", ".item-jasa", function () {
    const isEditTable =
        $(this).closest("table").attr("id") === "editItemsTable";
    if (isEditTable) {
        calculateEditTotalAkhir();
    } else {
        calculateTotalAkhir();
    }
});

// Recalculate when PPN checkbox changes
$("#ppn_checkbox, #edit_ppn_checkbox").on("change", function () {
    const iEdit = $(this).attr("id").startsWith("edit_");
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
    document.querySelectorAll("#itemsBody tr").forEach(function (row) {
        const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
        const price = parseFloat(row.querySelector(".item-price").value) || 0;
        dpp += qty * price;
    });
    return parseInt(dpp);
}

function calculateEditDPP() {
    // Calculate DPP from items
    let dpp = 0;
    document.querySelectorAll("#editItemsBody tr").forEach(function (row) {
        const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
        const price = parseFloat(row.querySelector(".item-price").value) || 0;
        dpp += qty * price;
    });
    return parseInt(dpp);
}

function calculateJasaDPP() {
    let dpp = 0;
    document.querySelectorAll("#itemsBody tr").forEach(function (row) {
        const isJasa = row.querySelector(".item-jasa")?.checked || false;
        if (!isJasa) return;
        const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
        const price = parseFloat(row.querySelector(".item-price").value) || 0;
        dpp += qty * price;
    });
    return parseInt(dpp);
}

function calculateEditJasaDPP() {
    let dpp = 0;
    document.querySelectorAll("#editItemsBody tr").forEach(function (row) {
        const isJasa = row.querySelector(".item-jasa")?.checked || false;
        if (!isJasa) return;
        const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
        const price = parseFloat(row.querySelector(".item-price").value) || 0;
        dpp += qty * price;
    });
    return parseInt(dpp);
}

function calculateBarangDPP() {
    let dpp = 0;
    document.querySelectorAll("#itemsBody tr").forEach(function (row) {
        const isJasa = row.querySelector(".item-jasa")?.checked || false;
        if (isJasa) return;
        const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
        const price = parseFloat(row.querySelector(".item-price").value) || 0;
        dpp += qty * price;
    });
    return parseInt(dpp);
}

function calculateEditBarangDPP() {
    let dpp = 0;
    document.querySelectorAll("#editItemsBody tr").forEach(function (row) {
        const isJasa = row.querySelector(".item-jasa")?.checked || false;
        if (isJasa) return;
        const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
        const price = parseFloat(row.querySelector(".item-price").value) || 0;
        dpp += qty * price;
    });
    return parseInt(dpp);
}

function calculatePPN() {
    const dpp = calculateDPP();
    const ppnCheckbox = document.getElementById("ppn_checkbox");

    let ppnNominal = 0;
    if (ppnCheckbox && ppnCheckbox.checked) {
        ppnNominal = Math.round(dpp * 0.11); // PPN 11%
    }

    const ppnField = document.getElementById("ppn_nominal");
    if (ppnField) {
        ppnField.value = ppnNominal.toLocaleString("id-ID");
    }

    return ppnNominal;
}

function calculateEditPPN() {
    const dpp = calculateEditDPP();
    const ppnCheckbox = document.getElementById("edit_ppn_checkbox");

    let ppnNominal = 0;
    if (ppnCheckbox && ppnCheckbox.checked) {
        ppnNominal = Math.round(dpp * 0.11); // PPN 11%
    }

    const ppnField = document.getElementById("edit_ppn_nominal");
    if (ppnField) {
        ppnField.value = ppnNominal.toLocaleString("id-ID");
    }

    return ppnNominal;
}

function calculatePPH22() {
    const barangDPP = calculateBarangDPP();
    const tarif = parseFloat(document.getElementById("tarif_pajak").value) || 0;
    let pph = 0;
    if (tarif > 0 && barangDPP > 2000000) {
        pph = Math.round((barangDPP * tarif) / 100);
    }
    const field = document.getElementById("pph_22_nominal");
    if (field) field.value = "Rp " + pph.toLocaleString("id-ID");
    const info = document.getElementById("pph_22_info");
    if (info) {
        if (tarif > 0) {
            info.textContent =
                barangDPP <= 2000000
                    ? "DPP Barang Rp " +
                      barangDPP.toLocaleString("id-ID") +
                      " ≤ 2jt → tidak dipotong"
                    : "DPP Barang Rp " + barangDPP.toLocaleString("id-ID");
            info.style.display = "";
        } else {
            info.style.display = "none";
        }
    }
    return pph;
}

function calculatePPH23() {
    const jasaDPP = calculateJasaDPP();
    const tarif =
        parseFloat(document.getElementById("tarif_pajak_23").value) || 0;
    let pph = 0;
    if (tarif > 0 && jasaDPP > 0) {
        pph = Math.round((jasaDPP * tarif) / 100);
    }
    const field = document.getElementById("pph_23_nominal");
    if (field) field.value = "Rp " + pph.toLocaleString("id-ID");
    const info = document.getElementById("pph_23_info");
    if (info) {
        if (tarif > 0) {
            info.textContent = "DPP Jasa Rp " + jasaDPP.toLocaleString("id-ID");
            info.style.display = "";
        } else {
            info.style.display = "none";
        }
    }
    return pph;
}

function calculatePPH() {
    const pph22 = calculatePPH22();
    const pph23 = calculatePPH23();
    const total = pph22 + pph23;
    const field = document.getElementById("pph_nominal");
    if (field) field.value = "Rp " + total.toLocaleString("id-ID");
    return total;
}

function calculateEditPPH22() {
    const barangDPP = calculateEditBarangDPP();
    const tarif =
        parseFloat(document.getElementById("edit_tarif_pajak").value) || 0;
    let pph = 0;
    if (tarif > 0 && barangDPP > 2000000) {
        pph = Math.round((barangDPP * tarif) / 100);
    }
    const field = document.getElementById("edit_pph_22_nominal");
    if (field) field.value = "Rp " + pph.toLocaleString("id-ID");
    const info = document.getElementById("edit_pph_22_info");
    if (info) {
        if (tarif > 0) {
            info.textContent =
                barangDPP <= 2000000
                    ? "DPP Barang Rp " +
                      barangDPP.toLocaleString("id-ID") +
                      " ≤ 2jt → tidak dipotong"
                    : "DPP Barang Rp " + barangDPP.toLocaleString("id-ID");
            info.style.display = "";
        } else {
            info.style.display = "none";
        }
    }
    return pph;
}

function calculateEditPPH23() {
    const jasaDPP = calculateEditJasaDPP();
    const tarif =
        parseFloat(document.getElementById("edit_tarif_pajak_23").value) || 0;
    let pph = 0;
    if (tarif > 0 && jasaDPP > 0) {
        pph = Math.round((jasaDPP * tarif) / 100);
    }
    const field = document.getElementById("edit_pph_23_nominal");
    if (field) field.value = "Rp " + pph.toLocaleString("id-ID");
    const info = document.getElementById("edit_pph_23_info");
    if (info) {
        if (tarif > 0) {
            info.textContent = "DPP Jasa Rp " + jasaDPP.toLocaleString("id-ID");
            info.style.display = "";
        } else {
            info.style.display = "none";
        }
    }
    return pph;
}

function calculateEditPPH() {
    const pph22 = calculateEditPPH22();
    const pph23 = calculateEditPPH23();
    const total = pph22 + pph23;
    const field = document.getElementById("edit_pph_nominal");
    if (field) field.value = "Rp " + total.toLocaleString("id-ID");
    return total;
}

function calculateTotalAkhir() {
    const dppDisplay = document.getElementById("dpp_display");
    const dpp = calculateDPP();
    const ppn = calculatePPN();
    const pph = calculatePPH();

    // Update DPP display
    if (dppDisplay) {
        dppDisplay.value = "Rp " + dpp.toLocaleString("id-ID");
    }

    // Total Akhir = DPP + PPN - PPH
    const totalAkhir = dpp + ppn - pph;

    const totalField = document.getElementById("total_akhir_display");
    if (totalField) {
        totalField.value = "Rp " + totalAkhir.toLocaleString("id-ID");
    }

    return totalAkhir;
}

function calculateEditTotalAkhir() {
    const dppDisplay = document.getElementById("edit_dpp_display");
    const dpp = calculateEditDPP();
    const ppn = calculateEditPPN();
    const pph = calculateEditPPH();

    // Update DPP display
    if (dppDisplay) {
        dppDisplay.value = "Rp " + dpp.toLocaleString("id-ID");
    }

    // Total Akhir = DPP + PPN - PPH
    const totalAkhir = dpp + ppn - pph;

    const totalField = document.getElementById("edit_total_akhir_display");
    if (totalField) {
        totalField.value = "Rp " + totalAkhir.toLocaleString("id-ID");
    }

    return totalAkhir;
}
