document.addEventListener('DOMContentLoaded', function() {
    // Nonaktifkan peringatan DataTables
    $.fn.dataTable.ext.errMode = 'none';
    
    // Inisialisasi Select2 untuk dropdown filter
    $('#filter-jenis').select2({
        placeholder: "Pilih jenis kegiatan",
        allowClear: true,
        width: '100%',
        dropdownParent: $('.filter-dropdown .dropdown-menu')
    });
    
    $('#filter-creator').select2({
        placeholder: "Pilih pembuat laporan",
        allowClear: true,
        width: '100%',
        dropdownParent: $('.filter-dropdown .dropdown-menu')
    });
    
    // Inisialisasi datepicker
    $('#filter-tahun').datepicker({
        format: 'yyyy',
        viewMode: 'years', 
        minViewMode: 'years',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom',
        language: 'id'
    });
    
    $('#filter-bulan').datepicker({
        format: 'mm',
        viewMode: 'months',
        minViewMode: 'months',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom',
        language: 'id'
    });
    
    // Mencegah dropdown menutup saat klik di dalam
    $('.filter-dropdown .dropdown-menu').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Inisialisasi DataTables
    const laporanTable = $('#laporanTable').DataTable({
        responsive: true,
        language: {
            search: '_INPUT_',
            searchPlaceholder: 'Cari Laporan...',
            lengthMenu: '_MENU_',
            paginate: {
                previous: '<i class="feather-chevron-left"></i>',
                next: '<i class="feather-chevron-right"></i>'
            },
            emptyTable: "Tidak ada data laporan",
            zeroRecords: "Tidak ada laporan yang sesuai dengan filter"
        },
        columnDefs: [{ 
            targets: [-1], 
            orderable: false 
        }]
    });
    
    // FILTER: Terapkan filter
    $('#apply-filter').on('click', function() {
        console.log("Menerapkan filter...");
        
        // Ambil nilai filter
        const jenis = $('#filter-jenis').val() || '';
        const tahun = $('#filter-tahun').val() || '';
        const bulan = $('#filter-bulan').val() || '';
        const creatorId = $('#filter-creator').val() || '';
        
        console.log("Filter values:", {
            jenis: jenis,
            tahun: tahun,
            bulan: bulan,
            creator: creatorId
        });
        
        // Simpan filter ke session melalui AJAX
        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('filters[jenis]', jenis);
        formData.append('filters[tahun]', tahun);
        formData.append('filters[bulan]', bulan);
        formData.append('filters[creator]', creatorId);
        
        fetch('/laporan-pts/save-filter-to-session', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            body: new URLSearchParams({
                'filters[jenis]': jenis,
                'filters[tahun]': tahun,
                'filters[bulan]': bulan,
                'filters[creator]': creatorId
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Filter disimpan ke session:', data);
        })
        .catch(error => {
            console.error('Error saat menyimpan filter:', error);
        });
        
        // Reset pencarian DataTables
        laporanTable.search('').columns().search('').draw();
        
        // Implementasi filter menggunakan jQuery untuk lebih baik
        $('.single-item').each(function() {
            const $row = $(this);
            let showRow = true;
            
            // 1. Filter berdasarkan jenis kegiatan
            if (jenis && $row.find('td:nth-child(3)').text().trim() !== jenis) {
                showRow = false;
            }
            
            // 2. Filter berdasarkan tahun
            if (tahun && !$row.find('td:nth-child(2)').text().includes(tahun)) {
                showRow = false;
            }
            
            // 3. Filter berdasarkan bulan
            if (bulan) {
                const bulanNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                const bulanIndex = parseInt(bulan, 10) - 1;
                
                if (bulanIndex >= 0 && bulanIndex < 12) {
                    const namaBulan = bulanNames[bulanIndex];
                    if (!$row.find('td:nth-child(2)').text().includes(namaBulan)) {
                        showRow = false;
                    }
                }
            }
            
            // 4. Filter berdasarkan pembuat laporan (user_id)
            if (creatorId && $row.data('user-id') != creatorId) {
                showRow = false;
            }
            
            // Tampilkan atau sembunyikan baris berdasarkan hasil filter
            if (showRow) {
                $row.show();
            } else {
                $row.hide();
            }
        });
        
        // Tutup dropdown
        $('.dropdown-toggle').dropdown('hide');
        
        // Pesan jika tidak ada hasil
        if ($('.single-item:visible').length === 0) {
            if ($('#no-results-message').length === 0) {
                $('#laporanTable tbody').append('<tr id="no-results-message"><td colspan="6" class="text-center">Tidak ada laporan yang sesuai dengan filter</td></tr>');
            }
        } else {
            $('#no-results-message').remove();
        }
    });
    
    // Reset Filter
    $('#reset-filter').on('click', function() {
        console.log("Mereset filter...");
        
        // Reset nilai filter
        $('#filter-jenis').val('').trigger('change');
        $('#filter-tahun').val('');
        $('#filter-bulan').val('');
        $('#filter-creator').val('').trigger('change');

        // Reset filter di session
        fetch('/laporan-pts/save-filter-to-session', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            body: new URLSearchParams({
                'filters': null
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Filter di session direset:', data);
        })
        .catch(error => {
            console.error('Error saat mereset filter:', error);
        });
        
        // Tampilkan semua baris
        $('.single-item').show();
        $('#no-results-message').remove();
        
        // Tutup dropdown
        $('.dropdown-toggle').dropdown('hide');
    });
    
    // Modifikasi tombol PDF dan Print untuk mengirim filter yang aktif
    $('#export-pdf').on('click', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        
        // Redirect ke URL export PDF (filter sudah tersimpan di session)
        window.location.href = url;
    });
    
    $('#print-view').on('click', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        
        // Buka URL print dalam tab baru (filter sudah tersimpan di session)
        window.open(url, '_blank');
    });
    
    // Konfirmasi sebelum menghapus
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        
        if (confirm('Apakah Anda yakin ingin menghapus laporan ini?')) {
            $(this).closest('form').submit();
        }
    });
});