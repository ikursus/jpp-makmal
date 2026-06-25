<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyelenggaraan - Sistem Pengurusan Barangan Makmal JPP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #003366 0%, #004d80 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .maintenance-container {
            text-align: center;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.1);
            padding: 60px 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .maintenance-icon {
            font-size: 80px;
            margin-bottom: 24px;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .maintenance-container h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 16px;
            color: white;
        }
        .maintenance-container p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 12px;
            line-height: 1.6;
        }
        .maintenance-container .message {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 12px;
            margin: 24px 0;
            font-size: 14px;
            border-left: 4px solid #f59e0b;
        }
        .maintenance-container .contact {
            margin-top: 32px;
            font-size: 14px;
            opacity: 0.8;
        }
        .maintenance-container .contact a {
            color: #fbbf24;
            text-decoration: none;
            font-weight: 600;
        }
        .maintenance-container .contact a:hover {
            text-decoration: underline;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            opacity: 0.6;
        }
        @media (max-width: 768px) {
            .maintenance-container {
                padding: 40px 24px;
            }
            .maintenance-container h1 {
                font-size: 24px;
            }
            .maintenance-icon {
                font-size: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">🔧</div>
        <h1>Penyelenggaraan Sistem</h1>
        <p>Maaf, sistem ini sedang undergoing penyelenggaraan.</p>
        <p>Kami sedang melakukan penambahbaikan untuk memberikan perkhidmatan yang lebih baik.</p>

        <div class="message">
            <strong>Sila cuba lagi later.</strong><br>
            Kami berharap dapat kembalikan sistem dengan secepat mungkin.
        </div>

        <div class="contact">
            Untuk sebarang pertanyaan, sila hubungi:<br>
            <a href="mailto:{{ config('jpp-config.general.site_email', 'admin@jpp.gov.my') }}">
                {{ config('jpp-config.general.site_email', 'admin@jpp.gov.my') }}
            </a>
        </div>

        <div class="footer">
            © {{ config('jpp-config.general.site_year', date('Y')) }} {{ config('jpp-config.general.site_copyright', 'Jabatan Perkhidmatan Pembetungan Sabah') }}. Hak Cipta Terpelihara.
        </div>
    </div>
</body>
</html>
