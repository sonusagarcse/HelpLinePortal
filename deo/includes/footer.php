    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const toggleBtn = document.getElementById('sidebarCollapse');
        const closeBtn = document.getElementById('sidebarCollapseMobile');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        }

        toggleBtn?.addEventListener('click', toggleSidebar);
        closeBtn?.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>
