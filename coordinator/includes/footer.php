    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function () {
            // Sidebar mobile toggle
            $('#sidebarCollapse, #sidebarOverlay').on('click', function () {
                $('#sidebar, #content, #sidebarOverlay').toggleClass('active');
            });
            
            // Auto close sidebar on link click (mobile)
            $('#sidebar ul li a').on('click', function() {
                if ($(window).width() <= 991) {
                    $('#sidebar, #content, #sidebarOverlay').removeClass('active');
                }
            });
            
            // Handle window resize
            $(window).resize(function() {
                if ($(window).width() > 991) {
                    $('#sidebar, #content, #sidebarOverlay').removeClass('active');
                }
            });
        });
    </script>
</body>
</html>
