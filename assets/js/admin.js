/**
 * ADMIN PANEL - JavaScript Functions
 * Khusus untuk halaman admin
 */

document.addEventListener('DOMContentLoaded', function() {
    initAdminPanel();
    initDataTables();
    initCharts();
    initFileUpload();
});

// ==========================================
// ADMIN PANEL INITIALIZATION
// ==========================================
function initAdminPanel() {
    console.log('🛠️ Admin Panel Loaded');
    
    // Auto-hide success messages
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-success');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-action="delete"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus ini?')) {
                e.preventDefault();
            }
        });
    });
}

// ==========================================
// DATA TABLES (Enhanced)
// ==========================================
function initDataTables() {
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        addSearchFilter(table);
        addSortable(table);
        addPagination(table);
    });
}

function addSearchFilter(table) {
    const searchBox = document.createElement('input');
    searchBox.type = 'text';
    searchBox.className = 'form-control mb-3';
    searchBox.placeholder = '🔍 Cari...';
    
    table.parentElement.insertBefore(searchBox, table);
    
    searchBox.addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
}

function addSortable(table) {
    const headers = table.querySelectorAll('thead th');
    
    headers.forEach((header, index) => {
        header.style.cursor = 'pointer';
        header.innerHTML += ' <i class="bi bi-arrow-down-up"></i>';
        
        header.addEventListener('click', () => {
            sortTable(table, index);
        });
    });
}

function sortTable(table, column) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    const sorted = rows.sort((a, b) => {
        const aText = a.cells[column].textContent.trim();
        const bText = b.cells[column].textContent.trim();
        
        return aText.localeCompare(bText, undefined, { numeric: true });
    });
    
    // Toggle sort direction
    if (table.dataset.sortDirection === 'asc') {
        sorted.reverse();
        table.dataset.sortDirection = 'desc';
    } else {
        table.dataset.sortDirection = 'asc';
    }
    
    tbody.innerHTML = '';
    sorted.forEach(row => tbody.appendChild(row));
}

function addPagination(table) {
    // Simple pagination - 10 items per page
    const rowsPerPage = 10;
    const rows = table.querySelectorAll('tbody tr');
    
    if (rows.length <= rowsPerPage) return;
    
    // Implementation here...
}

// ==========================================
// CHARTS & STATISTICS
// ==========================================
function initCharts() {
    // Dashboard Statistics Animation
    const statNumbers = document.querySelectorAll('.stat-card h3, .stat-card h4');
    
    statNumbers.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        animateCounter(stat, 0, finalValue, 1000);
    });
}

function animateCounter(element, start, end, duration) {
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(progress * (end - start) + start);
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            element.textContent = end;
        }
    }
    
    requestAnimationFrame(update);
}

// ==========================================
// BOOK MANAGEMENT
// ==========================================
function editBook(book) {
    const modal = document.getElementById('editBookModal');
    if (!modal) return;
    
    // Populate form fields
    document.getElementById('edit_book_id').value = book.id;
    document.getElementById('edit_title').value = book.title;
    document.getElementById('edit_author').value = book.author;
    document.getElementById('edit_description').value = book.description;
    document.getElementById('edit_category').value = book.category;
    document.getElementById('edit_file_url').value = book.file_url;
    document.getElementById('edit_is_premium').checked = book.is_premium == 1;
    
    // Show modal
    new bootstrap.Modal(modal).show();
}

function deleteBook(bookId, bookTitle) {
    if (confirm(`Hapus buku "${bookTitle}"?\n\nBuku yang dihapus tidak dapat dikembalikan.`)) {
        window.location.href = `manage_books.php?delete=${bookId}`;
    }
}

// ==========================================
// USER MANAGEMENT
// ==========================================
function viewUserDetail(user) {
    const modal = document.getElementById('userDetailModal');
    if (!modal) return;
    
    const content = `
        <div class="text-center mb-3">
            <i class="bi bi-person-circle" style="font-size: 4rem; color: #6366f1;"></i>
        </div>
        <table class="table table-bordered">
            <tr>
                <th width="40%">User ID:</th>
                <td>#${user.id}</td>
            </tr>
            <tr>
                <th>Username:</th>
                <td><strong>${user.username}</strong></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>${user.email}</td>
            </tr>
            <tr>
                <th>Role:</th>
                <td><span class="badge bg-primary">${user.role}</span></td>
            </tr>
            <tr>
                <th>Bergabung:</th>
                <td>${formatDate(user.created_at)}</td>
            </tr>
            <tr>
                <th>Langganan Aktif:</th>
                <td>${user.active_subs > 0 ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>'}</td>
            </tr>
            <tr>
                <th>Total Favorit:</th>
                <td><span class="badge bg-info">${user.total_favorites || 0} buku</span></td>
            </tr>
        </table>
    `;
    
    document.getElementById('userDetailContent').innerHTML = content;
    new bootstrap.Modal(modal).show();
}

function deleteUser(userId, username) {
    const confirmed = confirm(
        `Hapus user "${username}"?\n\n` +
        `⚠️ PERINGATAN:\n` +
        `- Semua data favorit akan terhapus\n` +
        `- Semua data langganan akan terhapus\n` +
        `- Aksi ini tidak dapat dibatalkan\n\n` +
        `Lanjutkan?`
    );
    
    if (confirmed) {
        window.location.href = `manage_users.php?delete=${userId}`;
    }
}

// ==========================================
// FILE UPLOAD HANDLER
// ==========================================
function initFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            
            // Validate file
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                alert('Format file tidak valid. Gunakan JPG, JPEG, atau PNG.');
                this.value = '';
                return;
            }
            
            // Check file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                this.value = '';
                return;
            }
            
            // Preview image
            previewUploadedImage(file, this);
        });
    });
}

function previewUploadedImage(file, input) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        let preview = input.nextElementSibling;
        if (!preview || !preview.classList.contains('image-preview')) {
            preview = document.createElement('img');
            preview.className = 'image-preview img-thumbnail mt-2';
            preview.style.maxWidth = '200px';
            input.parentNode.insertBefore(preview, input.nextSibling);
        }
        preview.src = e.target.result;
    };
    
    reader.readAsDataURL(file);
}

// ==========================================
// BULK ACTIONS
// ==========================================
function initBulkActions() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    
    if (!selectAllCheckbox) return;
    
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionButton();
    });
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButton);
    });
    
    function updateBulkActionButton() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (bulkActionBtn) {
            bulkActionBtn.disabled = checkedCount === 0;
            bulkActionBtn.textContent = `Tindakan (${checkedCount} dipilih)`;
        }
    }
}

function executeBulkAction(action) {
    const checkedItems = document.querySelectorAll('.item-checkbox:checked');
    const ids = Array.from(checkedItems).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('Pilih minimal 1 item');
        return;
    }
    
    if (!confirm(`Jalankan "${action}" untuk ${ids.length} item?`)) {
        return;
    }
    
    // Send AJAX request
    console.log(`Executing ${action} for IDs:`, ids);
    // Implementation: fetch('/admin/bulk-action.php', { method: 'POST', body: JSON.stringify({ action, ids }) })
}

// ==========================================
// EXPORT FUNCTIONS
// ==========================================
function exportToCSV(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = Array.from(cols).map(col => {
            return '"' + col.textContent.trim().replace(/"/g, '""') + '"';
        });
        csv.push(rowData.join(','));
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `export_${Date.now()}.csv`;
    link.click();
}

function exportToPDF() {
    window.print();
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

function showAdminNotification(type, message) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// ==========================================
// REAL-TIME UPDATES (Optional)
// ==========================================
function startRealTimeUpdates() {
    // Poll for updates every 30 seconds
    setInterval(() => {
        updateDashboardStats();
    }, 30000);
}

function updateDashboardStats() {
    // Fetch latest stats via AJAX
    // Implementation: fetch('/admin/get-stats.php').then(...)
    console.log('Updating dashboard stats...');
}

// ==========================================
// EXPORT ADMIN FUNCTIONS
// ==========================================
window.adminPanel = {
    editBook,
    deleteBook,
    viewUserDetail,
    deleteUser,
    exportToCSV,
    exportToPDF,
    executeBulkAction,
    showAdminNotification
};

console.log('🛠️ Admin JavaScript loaded!');