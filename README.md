# 🥋 Pencak Silat Scoring System

Aplikasi Laravel untuk **scoring kategori seni** (solo, tunggal, regu, ganda) dengan dukungan **system pool realtime** menggunakan **WebSocket**.  
Didesain agar mudah dijalankan di perangkat lokal maupun server.

---

## ✨ Fitur Utama
- **Scoring Seni**
  - Solo
  - Tunggal
  - Regu
  - Ganda
- **Realtime Pool System**
  - Komunikasi antar juri & peserta via WebSocket
  - Update skor langsung tanpa refresh
- **WebSocket Localhost**
  - Server realtime berjalan di perangkat lokal
  - Mendukung broadcast skor ke semua client

---

## 📂 Struktur Project
- `app/` → Core Laravel application
- `routes/` → Routing web & API
- `resources/views/` → Blade templates untuk UI scoring
- `database/migrations/` → Struktur tabel scoring & pool
- `.env` → Konfigurasi environment (DB, WebSocket key, dll)
- `database.sql` → Dump database untuk setup cepat

---

## 🚀 Cara Menjalankan
1. Clone repository:
   ```bash
   git clone https://github.com/username/nama-repo.git
   cd nama-repo
