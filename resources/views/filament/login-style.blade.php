<style>
    /* 1. Ganti Background Halaman */
    body {
        background-image: url('{{ asset("images/login-bg.jpg") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    /* 2. Gelapkan background sedikit agar tulisan terbaca (Overlay) */
    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Hitam transparan 50% */
        z-index: -1;
    }

    /* 3. Modifikasi Kotak Login (Efek Kaca / Glassmorphism) */
    .fi-simple-layout-card {
        background-color: rgba(255, 255, 255, 0.85) !important; /* Putih transparan */
        backdrop-filter: blur(10px); /* Efek blur di belakang kotak */
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }

    /* Jika mode Dark Mode aktif, sesuaikan warnanya */
    .dark .fi-simple-layout-card {
        background-color: rgba(17, 24, 39, 0.8) !important; /* Hitam transparan */
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* 4. Ubah Logo / Judul Login jadi lebih besar/menonjol */
    .fi-simple-layout-header {
        margin-bottom: 2rem;
        transform: scale(1.2); /* Memperbesar logo */
    }
</style>