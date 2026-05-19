
<script>

const saved = localStorage.getItem('theme');
if (saved === 'light') {
    document.body.classList.add('light-mode');
    const btn = document.getElementById('theme-btn');
    if (btn) btn.textContent = '🌙 Dark Mode';
}

function toggleTheme() {
    const isLight = document.body.classList.toggle('light-mode');
    const btn = document.getElementById('theme-btn');

    if (isLight) {
        localStorage.setItem('theme', 'light');
        btn.textContent = '🌙 Dark Mode';
    } else {
        localStorage.setItem('theme', 'dark');
        btn.textContent = '☀️ Light Mode';
    }
}
</script>

</body>
</html>
