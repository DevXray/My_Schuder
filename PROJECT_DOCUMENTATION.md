# 📚 My Schuder - Portal Pembelajaran Online

## 🎯 Deskripsi Project
Sistem Manajemen Pembelajaran berbasis web menggunakan Laravel 11 dengan 3 role: **Admin**, **Dosen**, dan **Mahasiswa**.

---

## 🔐 User Roles & Permissions

### 1️⃣ **ADMIN (Administrator)**
**Akses Penuh ke Semua Fitur:**
- ✅ Mengelola semua users (CRUD)
- ✅ Mengelola Mahasiswa dan Dosen
- ✅ Mengelola Mata Kuliah
- ✅ Melihat semua Materi & Tugas
- ✅ Melihat Dashboard Admin dengan statistik lengkap
- ✅ Export data & laporan

### 2️⃣ **DOSEN (Pengajar)**
**Fokus pada Pengajaran:**
- ✅ Mengelola Materi (Create, Edit, Delete)
- ✅ Mengelola Tugas (Create, Edit, Delete, Grade)
- ✅ Melihat Jadwal Mengajar
- ✅ Melihat Progress Mahasiswa
- ✅ Menilai Pengumpulan Tugas
- ❌ TIDAK bisa mengelola users atau mahasiswa lain

### 3️⃣ **MAHASISWA (Peserta Didik)**
**Fokus pada Pembelajaran:**
- ✅ Melihat Materi Pembelajaran
- ✅ Mengerjakan & Mengumpulkan Tugas
- ✅ Melihat Jadwal Kelas
- ✅ Track Progress Belajar
- ✅ Chat dengan Dosen/Mahasiswa lain
- ❌ TIDAK bisa mengelola materi/tugas

---

## 📦 Instalasi & Setup

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM

### Langkah Instalasi

```bash
# 1. Clone/Copy project
cd d:\xampp\htdocs\My_Schuder

# 2. Install dependencies
composer install
npm install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Setup database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_schuder
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations
php artisan migrate:fresh

# 7. Seed database dengan data default
php artisan db:seed

# 8. Build assets
npm run build

# 9. Create storage link
php artisan storage:link

# 10. Start development server
php artisan serve
```

### Default Login Credentials

```
ADMIN:
Email: admin@schuder.ac.id
Password: admin123

DOSEN:
Email: budi.santoso@schuder.ac.id
Password: dosen123

MAHASISWA:
Email: mahasiswa1@student.schuder.ac.id
Password: mahasiswa123
```

---

## 🛠️ Yang Sudah Diimplementasi

### ✅ Authentication & Authorization
- [x] Login/Register dengan Laravel Breeze
- [x] Email Verification
- [x] Password Reset
- [x] Role-based Access Control (Middleware)
- [x] Profile Management

### ✅ User Management (Admin Only)
- [x] CRUD Users dengan role
- [x] CRUD Mahasiswa
- [x] CRUD Dosen
- [x] Bulk actions (delete, change role)

### ✅ Materi Pembelajaran
- [x] CRUD Materi (Admin & Dosen)
- [x] View Materi (All roles)
- [x] Upload file materi
- [x] Track progress materi

### ✅ Tugas & Pengumpulan
- [x] CRUD Tugas (Admin & Dosen)
- [x] Submit Tugas (Mahasiswa)
- [x] Grade Tugas (Dosen)
- [x] Download file soal/jawaban

### ✅ Jadwal
- [x] CRUD Jadwal
- [x] View jadwal per hari
- [x] Search jadwal

### ✅ Dashboard
- [x] Admin Dashboard dengan statistik
- [x] Student/Dosen Dashboard
- [x] Informasi terbaru & tugas deadline

### ✅ UI/UX
- [x] Responsive design
- [x] Header dengan profile dropdown
- [x] Sidebar navigation
- [x] Loading screen
- [x] Chatbot integration (Gemini AI)

---

## 🚧 Yang Perlu Ditambahkan

### ❌ PRIORITAS TINGGI

#### 1. **Dosen Dashboard Terpisah**
**Mengapa:** Saat ini Dosen masih pakai student dashboard
**File:** `app/Http/Controllers/DashboardController.php`
**Action:**
```php
// Tambahkan di DashboardController
private function dosenDashboard() {
    $stats = [
        'total_materi' => Materi::where('dosen_id', auth()->user()->dosen->id)->count(),
        'total_tugas' => Tugas::where('dosen_id', auth()->user()->dosen->id)->count(),
        'tugas_belum_dinilai' => Pengumpulan::whereHas('tugas', function($q) {
            $q->where('dosen_id', auth()->user()->dosen->id);
        })->where('status', 'submitted')->count(),
        'total_mahasiswa' => Mahasiswa::count(),
    ];
    
    return view('dashboard.dosen', compact('stats'));
}
```

#### 2. **Perbaiki Relationship Dosen-Materi-Tugas**
**Mengapa:** Model Materi & Tugas harus punya `dosen_id`
**File:** Perlu migration baru
```bash
php artisan make:migration add_dosen_id_to_materi_and_tugas_tables
```

#### 3. **Notifications System**
**Fitur:**
- Notifikasi tugas baru
- Notifikasi deadline mendekati
- Notifikasi nilai keluar
**File:** Perlu buat `notifications` table

#### 4. **File Upload Validation**
**Mengapa:** Belum ada validasi file size & type
**File:** `app/Http/Controllers/TugasController.php`
```php
'file_soal' => 'required|mimes:pdf,doc,docx|max:10240', // 10MB
'file_jawaban' => 'required|mimes:pdf,doc,docx,zip|max:10240',
```

#### 5. **Mata Kuliah Management**
**Status:** Controller ada tapi view belum lengkap
**Action:** Buat views untuk:
- `resources/views/matakuliah/index.blade.php`
- `resources/views/matakuliah/create.blade.php`
- `resources/views/matakuliah/edit.blade.php`

---

### ❌ PRIORITAS MENENGAH

#### 6. **Peserta/Kelas Management**
**Fitur:**
- Mahasiswa join ke mata kuliah
- Dosen lihat peserta kelasnya
- Export daftar peserta

#### 7. **Attendance System**
**Fitur:**
- Presensi online per jadwal
- QR Code check-in
- Rekap absensi

#### 8. **Discussion Forum**
**Fitur:**
- Forum diskusi per materi
- Comment & Reply
- Like/Upvote

#### 9. **Progress Tracking**
**Fitur:**
- Progress bar completion materi
- Certificate setelah selesai
- Achievement badges

#### 10. **Export & Reporting**
**Fitur:**
- Export nilai ke Excel
- Export absensi
- Generate transcript

---

### ❌ PRIORITAS RENDAH (Nice to Have)

#### 11. **Email Notifications**
- Email saat tugas baru
- Email reminder deadline
- Email nilai keluar

#### 12. **Calendar View**
- Kalender akademik
- View jadwal per bulan
- Export to Google Calendar

#### 13. **Live Chat Enhancement**
- Real-time chat dengan Pusher/WebSocket
- Group chat per kelas
- File sharing in chat

#### 14. **Mobile Responsive Improvements**
- PWA support
- Offline mode
- Push notifications

#### 15. **Gamification**
- Points system
- Leaderboard
- Daily streak

---

## 📂 Struktur Project

```
My_Schuder/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdministratorController.php  ✅
│   │   │   ├── DashboardController.php      ✅
│   │   │   ├── MateriController.php         ✅
│   │   │   ├── TugasController.php          ✅
│   │   │   └── JadwalController.php         ✅
│   │   └── Middleware/
│   │       └── CheckRole.php                ✅
│   └── Models/
│       ├── User.php                         ✅
│       ├── Role.php                         ✅
│       ├── Mahasiswa.php                    ✅
│       ├── Dosen.php                        ✅
│       ├── Materi.php                       ✅
│       └── Tugas.php                        ✅
├── database/
│   ├── migrations/
│   │   ├── create_roles_table.php           ✅ BARU
│   │   └── update_users_table_with_role_id.php ✅
│   └── seeders/
│       ├── RoleSeeder.php                   ✅
│       ├── UserSeeder.php                   ✅ BARU
│       └── DatabaseSeeder.php               ✅
├── resources/
│   └── views/
│       ├── dashboard/
│       │   ├── admin.blade.php              ✅
│       │   └── dosen.blade.php              ❌ PERLU DIBUAT
│       ├── materi/                          ✅
│       ├── tugas/                           ✅
│       └── partials/
│           ├── header.blade.php             ✅
│           └── sidebar.blade.php            ✅
└── routes/
    └── web.php                              ✅ UPDATED
```

---

## 🐛 Bug Fixes yang Sudah Dilakukan

1. ✅ **Migration `roles` table** - CREATED
2. ✅ **UserSeeder dengan role_id** - CREATED
3. ✅ **DatabaseSeeder urutan seeding** - FIXED
4. ✅ **Route middleware protection** - APPLIED
5. ✅ **CheckRole middleware** - VERIFIED

---

## 🧪 Testing

### Manual Testing Checklist

#### Admin Role
- [ ] Login sebagai admin
- [ ] Akses dashboard admin
- [ ] CRUD users
- [ ] CRUD mahasiswa
- [ ] CRUD dosen
- [ ] View all materi & tugas

#### Dosen Role
- [ ] Login sebagai dosen
- [ ] Create materi baru
- [ ] Create tugas baru
- [ ] Grade pengumpulan tugas
- [ ] TIDAK bisa akses user management

#### Mahasiswa Role
- [ ] Login sebagai mahasiswa
- [ ] View materi
- [ ] Submit tugas
- [ ] Track progress
- [ ] TIDAK bisa create materi/tugas

---

## 📞 Support

Jika ada pertanyaan atau bug:
1. Check log di `storage/logs/laravel.log`
2. Jalankan `php artisan cache:clear`
3. Jalankan `php artisan config:clear`

---

## 📜 License
MIT License

---

**Last Updated:** 25 Januari 2026
**Version:** 1.0.0
