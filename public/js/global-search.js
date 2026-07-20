document.addEventListener('DOMContentLoaded', function() {
    // Dapatkan elemen input pencarian
    const searchInput = document.querySelector('.search-input-field');
    const searchForm = document.querySelector('.search-form');
    const clearButton = document.querySelector('.search-form .btn-close');
    
    // Pastikan elemen ada sebelum menambahkan event listener
    if (!searchInput || !searchForm || !clearButton) {
        console.error('Elemen pencarian tidak ditemukan');
        return;
    }
    
    // Token CSRF untuk request ajax
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    
    if (!csrfTokenMeta) {
        console.error('CSRF token tidak ditemukan, tambahkan meta tag csrf-token di head');
        return;
    }
    
    const csrfToken = csrfTokenMeta.getAttribute('content');
    
    // Tambahkan event listener untuk input pencarian (dengan debounce)
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        
        const query = this.value.trim();
        
        // Kosongkan hasil pencarian jika query kosong
        if (query === '') {
            clearSearchResults();
            return;
        }
        
        // Tunggu 300ms sebelum melakukan pencarian (debounce)
        debounceTimer = setTimeout(function() {
            performSearch(query);
        }, 300);
    });
    
    // Tambahkan event listener untuk tombol clear
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        clearSearchResults();
    });
    
    // Fungsi untuk melakukan pencarian
    function performSearch(query) {
        // Tampilkan loading indicator
        showLoadingIndicator();
        
        // Lakukan request AJAX ke endpoint pencarian
        fetch('/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ query: query })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Response not OK: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Handle error yang dikirim oleh server
            if (data.error) {
                throw new Error(data.message || 'Terjadi kesalahan pada server');
            }
            
            // Hapus loading indicator
            hideLoadingIndicator();
            
            // Tampilkan hasil pencarian
            displaySearchResults(data);
        })
        .catch(error => {
            console.error('Error in search:', error);
            hideLoadingIndicator();
            displayErrorMessage(error.message || 'Terjadi kesalahan saat melakukan pencarian');
        });
    }
    
    // Fungsi untuk menampilkan loading indicator
    function showLoadingIndicator() {
        // Bersihkan hasil yang ada
        clearSearchResults();
        
        // Buat elemen search results jika belum ada
        let searchResults = document.querySelector('.search-results');
        
        if (!searchResults) {
            searchResults = document.createElement('div');
            searchResults.className = 'search-results';
            
            // Tambahkan setelah divider
            const divider = document.querySelector('.nxl-search-dropdown .dropdown-divider');
            if (divider) {
                divider.after(searchResults);
            } else {
                // Jika tidak ada divider, tambahkan ke dropdown
                const dropdown = document.querySelector('.nxl-search-dropdown');
                if (dropdown) {
                    dropdown.appendChild(searchResults);
                }
            }
        }
        
        searchResults.innerHTML = `
            <div class="p-3 text-center">
                <div class="spinner-border text-primary spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Mencari...</span>
            </div>
        `;
    }
    
    // Fungsi untuk menyembunyikan loading indicator
    function hideLoadingIndicator() {
        // Akan diatasi oleh displaySearchResults atau displayErrorMessage
    }
    
    // Fungsi untuk menampilkan hasil pencarian
    function displaySearchResults(data) {
        // Bersihkan hasil yang ada
        clearSearchResults();
        
        // Buat elemen search results
        let searchResults = document.createElement('div');
        searchResults.className = 'search-results';
        
        // Tambahkan setelah divider
        const divider = document.querySelector('.nxl-search-dropdown .dropdown-divider');
        if (divider) {
            divider.after(searchResults);
        } else {
            // Jika tidak ada divider, tambahkan ke dropdown
            const dropdown = document.querySelector('.nxl-search-dropdown');
            if (dropdown) {
                dropdown.appendChild(searchResults);
            } else {
                console.error('Dropdown container tidak ditemukan');
                return;
            }
        }
        
        // Jika tidak ada hasil
        if (!data.results || data.results.length === 0) {
            searchResults.innerHTML = `
                <div class="p-3 text-center text-muted">
                    <i class="feather-info me-2"></i>Tidak ada hasil ditemukan
                </div>
            `;
            return;
        }
        
        // Jika ada hasil
        let resultsHtml = `<div class="p-2">`;
        
        // Kategorikan hasil berdasarkan jenis
        const resultsByType = {};
        
        data.results.forEach(item => {
            if (!resultsByType[item.type]) {
                resultsByType[item.type] = [];
            }
            resultsByType[item.type].push(item);
        });
        
        // Buat HTML untuk setiap kategori
        for (const type in resultsByType) {
            // Format nama tipe
            const formattedType = formatSearchType(type);
            
            resultsHtml += `
                <div class="search-category mb-2">
                    <h6 class="text-uppercase text-muted fs-11 px-2">${formattedType}</h6>
                    <div class="list-group list-group-flush">
            `;
            
            // Tampilkan maksimal 5 hasil per kategori
            const items = resultsByType[type].slice(0, 5);
            
            items.forEach(item => {
                const description = item.description ? escapeHtml(item.description) : '';

                resultsHtml += `
                    <a href="${item.url}" class="list-group-item list-group-item-action py-2">
                        <div class="d-flex align-items-center">
                            <div class="search-result-icon me-3">
                                <i class="${getIconForType(type)} text-primary"></i>
                            </div>
                            <div class="search-result-content">
                                <h6 class="mb-0 text-truncate">${highlightQuery(item.title, data.query)}</h6>
                                <small class="text-muted">${description}</small>
                            </div>
                        </div>
                    </a>
                `;
            });
            
            resultsHtml += `
                    </div>
                </div>
            `;
        }
        
        // Tambahkan link "Lihat semua hasil"
        resultsHtml += `
            <div class="text-center py-2 border-top">
                <a href="/search?q=${encodeURIComponent(data.query)}" class="fs-13 fw-semibold text-primary">
                    Lihat semua hasil
                </a>
            </div>
        `;
        
        resultsHtml += `</div>`;
        
        // Masukkan HTML ke dalam kontainer
        searchResults.innerHTML = resultsHtml;
    }
    
    // Fungsi untuk menampilkan pesan error
    function displayErrorMessage(message = 'Terjadi kesalahan saat melakukan pencarian') {
        // Bersihkan hasil yang ada
        clearSearchResults();
        
        // Buat elemen search results
        let searchResults = document.createElement('div');
        searchResults.className = 'search-results';
        
        // Tambahkan setelah divider
        const divider = document.querySelector('.nxl-search-dropdown .dropdown-divider');
        if (divider) {
            divider.after(searchResults);
        } else {
            // Jika tidak ada divider, tambahkan ke dropdown
            const dropdown = document.querySelector('.nxl-search-dropdown');
            if (dropdown) {
                dropdown.appendChild(searchResults);
            } else {
                console.error('Dropdown container tidak ditemukan');
                return;
            }
        }
        
        searchResults.innerHTML = `
            <div class="p-3 text-center text-danger">
                <i class="feather-alert-circle me-2"></i>${message}
            </div>
        `;
    }
    
    // Fungsi untuk membersihkan hasil pencarian
    function clearSearchResults() {
        const searchResults = document.querySelector('.search-results');
        if (searchResults) {
            searchResults.remove();
        }
    }
    
    // Fungsi helper untuk memformat jenis pencarian
    function formatSearchType(type) {
        switch (type) {
            case 'user':
                return 'Pengguna';
            case 'laporan':
                return 'Laporan PT';
            case 'perguruan_tinggi':
                return 'Perguruan Tinggi';
            case 'dokumen':
                return 'Dokumen';
            case 'page':
                return 'Halaman';
            default:
                return type.charAt(0).toUpperCase() + type.slice(1);
        }
    }
    
    // Fungsi helper untuk mendapatkan ikon berdasarkan jenis
    function getIconForType(type) {
        switch (type) {
            case 'user':
                return 'feather-user';
            case 'laporan':
                return 'feather-file-text';
            case 'perguruan_tinggi':
                return 'feather-home';
            case 'dokumen':
                return 'feather-file';
            case 'page':
                return 'feather-layout';
            default:
                return 'feather-search';
        }
    }
    
    // Fungsi untuk menyorot kata kunci di judul
    function highlightQuery(text, query) {
        if (!text) return '';
        
        const safeText = escapeHtml(text);
        
        if (!query) {
            return safeText;
        }
        
        try {
            const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
            return safeText.replace(regex, '<span class="bg-warning text-dark">$1</span>');
        } catch (e) {
            console.error('Error highlighting text:', e);
            return safeText;
        }
    }
    
    // Escape string untuk digunakan dalam regex
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    
    function escapeHtml(string) {
        return String(string)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
});
