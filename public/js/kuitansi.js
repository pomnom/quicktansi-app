// DataTables and SweetAlert2 are loaded via CDN in the view

$(document).ready(function () {
    // Destroy existing DataTable instance if exists
    if ($.fn.DataTable.isDataTable("#dataTable")) {
        $("#dataTable").DataTable().destroy();
    }

    // Initialize fresh DataTable with responsive
    $("#dataTable").DataTable({
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [0, 8] }, // Disable sorting on checkbox and Aksi column
            { responsivePriority: 1, targets: 0 }, // Checkbox always visible
            { responsivePriority: 2, targets: 2 }, // No. Buku
            { responsivePriority: 3, targets: -1 }, // Aksi column
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json",
            emptyTable: "Tidak ada data Kuitansi.",
            zeroRecords: "Tidak ada data yang cocok",
        },
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Semua"],
        ],
        autoWidth: false,
    });

    // SweetAlert for delete confirmation
    $(".btn-danger").on("click", function (e) {
        e.preventDefault();
        var form = $(this).closest("form");
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data ini akan dihapus permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Dark Mode Toggle with System Preference Support
    const darkModeToggle = $("#darkModeToggle");
    const body = $("body");

    // Function to get system dark mode preference
    function getSystemTheme() {
        return window.matchMedia &&
            window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light";
    }

    // Function to apply theme
    function applyTheme(theme) {
        if (theme === "dark") {
            body.addClass("dark-mode").removeClass("light-mode");
            darkModeToggle.prop("checked", true);
            darkModeToggle
                .siblings("label")
                .find("i")
                .removeClass("fa-moon")
                .addClass("fa-sun");
        } else {
            body.removeClass("dark-mode").addClass("light-mode");
            darkModeToggle.prop("checked", false);
            darkModeToggle
                .siblings("label")
                .find("i")
                .removeClass("fa-sun")
                .addClass("fa-moon");
        }
    }

    // Check for saved theme preference or use system preference
    let currentTheme = localStorage.getItem("theme");
    if (!currentTheme) {
        // No saved preference, use system preference
        currentTheme = getSystemTheme();
        localStorage.setItem("theme", currentTheme);
    }
    applyTheme(currentTheme);

    // Listen for system theme changes
    window
        .matchMedia("(prefers-color-scheme: dark)")
        .addEventListener("change", function (e) {
            if (!localStorage.getItem("theme")) {
                // Only auto-switch if user hasn't manually set a preference
                const newTheme = e.matches ? "dark" : "light";
                applyTheme(newTheme);
                localStorage.setItem("theme", newTheme);
            }
        });

    darkModeToggle.on("change", function () {
        const newTheme = $(this).is(":checked") ? "dark" : "light";
        localStorage.setItem("theme", newTheme);
        applyTheme(newTheme);
    });

    // SweetAlert for success/error messages
    // Note: Blade directives won't work in external JS files
    // These are handled in the view
});
