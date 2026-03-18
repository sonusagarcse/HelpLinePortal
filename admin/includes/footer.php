<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Sidebar toggle and overlay logic
    $('#sidebarCollapse, #sidebarOverlay').on('click', function () {
        $('#sidebar, #content, #sidebarOverlay').toggleClass('active');
    });

    // Close sidebar on mobile when window is resized to desktop
    $(window).on('resize', function() {
        if ($(window).width() > 991.98) {
            $('#sidebar, #content, #sidebarOverlay').removeClass('active');
        }
    });

    // Initialize DataTables
    $(document).ready(function () {
        if ($('.data-table').length) {
            $('.data-table').DataTable({
                "pageLength": 25,
                "order": [[0, "desc"]],
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        }
    });

    // Confirm delete
    function confirmDelete(message) {
        return confirm(message || 'Are you sure you want to delete this item?');
    }

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = `
                <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                    <div class="toast show align-items-center text-white bg-${type} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>
            `;
        $('body').append(toast);
        setTimeout(() => $('.toast').remove(), 3000);
    }
</script>
</body>

</html>
