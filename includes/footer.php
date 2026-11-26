
    </main>

    <!-- FOOTER -->

    <footer class="app-footer">

        &copy; <?= date('Y') ?> All right Reserved.

        <span class="fw-bold">
            HAKUZIMANA Jean Arsene. 224014854
        </span>

    </footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="/fitness-system/js/main.js"></script>

<script>

// Mobile sidebar toggle

const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');

if (menuToggle && sidebar) {

    menuToggle.addEventListener('click', function () {

        sidebar.classList.toggle('show');

    });

}

// Close sidebar when clicking outside on mobile

document.addEventListener('click', function(event) {

    if (
        window.innerWidth <= 991 &&
        sidebar &&
        menuToggle &&
        !sidebar.contains(event.target) &&
        !menuToggle.contains(event.target)
    ) {

        sidebar.classList.remove('show');

    }

});

</script>

</body>
</html>
