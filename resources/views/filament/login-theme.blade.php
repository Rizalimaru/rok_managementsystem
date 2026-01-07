<style>
    /* 1. BACKGROUND UTAMA */
    body {
        /* Ganti URL ini jika nama file beda */
        background-image: url('{{ asset("images/login-bg.jpg") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    /* Overlay Hitam (Supaya tulisan tetap terbaca walau background ramai) */
    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.8) 100%);
        z-index: -1;
    }

    /* 2. KARTU LOGIN (GLASSMORPHISM) */
    .fi-simple-layout-card {
        background-color: rgba(20, 20, 20, 0.65) !important; /* Hitam Transparan */
        backdrop-filter: blur(15px); /* Efek Blur Kaca */
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 215, 0, 0.2); /* Garis tepi tipis warna Emas */
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        border-radius: 16px;
    }

    /* 3. INPUT FIELDS */
    .fi-input {
        background-color: rgba(0, 0, 0, 0.5) !important; /* Input lebih gelap */
        border-color: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        transition: all 0.3s ease;
    }

    /* Efek saat input diklik (Focus) */
    .fi-input:focus-within {
        border-color: #fbbf24 !important; /* Warna Amber/Emas */
        box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.3) !important; /* Glow Emas */
    }

    /* 4. TOMBOL SIGN IN (GRADIENT EMAS) */
    .fi-btn-primary {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%) !important;
        border: none;
        font-weight: bold;
        letter-spacing: 0.5px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .fi-btn-primary:hover {
        transform: translateY(-2px); /* Efek naik sedikit saat hover */
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); /* Bayangan emas */
    }

    /* 5. LOGO / JUDUL */
    .fi-simple-layout-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .fi-simple-layout-header h1 {
        font-family: 'Cinzel', serif; /* Font gaya kerajaan (jika ada) */
        text-transform: uppercase;
        background: -webkit-linear-gradient(#fff, #cbd5e1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 1.8rem;
        font-weight: 800;
        text-shadow: 0px 2px 4px rgba(0,0,0,0.5);
    }

    /* Label text (Email/Password) */
    label {
        color: #e2e8f0 !important; /* Text putih agak abu */
        font-weight: 600;
    }
    
    /* Checkbox Remember Me */
    .fi-checkbox-input {
        border-color: #fbbf24 !important;
        color: #fbbf24 !important;
    }
</style>