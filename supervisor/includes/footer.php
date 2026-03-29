            </div> <!-- /content-area -->
        </div> <!-- /main-content -->
    </div> <!-- /wrapper -->

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            // Mobile Sidebar Toggle
            const mobileToggle = $('#mobileToggle');
            const sidebar = $('#sidebar');
            const sidebarOverlay = $('#sidebarOverlay');

            function toggleSidebar() {
                sidebar.toggleClass('show');
                sidebarOverlay.toggleClass('show');
            }

            mobileToggle.on('click', toggleSidebar);
            sidebarOverlay.on('click', toggleSidebar);
            
            // Default DataTables configuration
            $.extend(true, $.fn.dataTable.defaults, {
                dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records..."
                }
            });
        });
    </script>
</body>
</html>
