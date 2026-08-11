# ✅ TODO CHECKLIST - My Schuder Project

## 🔴 URGENT - Harus Diselesaikan Segera

- [x] Buat migration `roles` table
- [x] Buat UserSeeder dengan data default
- [x] Update DatabaseSeeder
- [x] Protect admin routes dengan middleware
- [x] **Dark Mode Implementation** ✨ NEW!
- [ ] **Test database migration & seeding**
  ```bash
  php artisan migrate:fresh --seed
  ```
- [ ] **Test login untuk 3 role**
  - Admin: admin@schuder.ac.id / admin123
  - Dosen: budi.santoso@schuder.ac.id / dosen123
  - Mahasiswa: mahasiswa1@student.schuder.ac.id / mahasiswa123

---

## 🟠 HIGH PRIORITY - Minggu Ini

### 1. Dashboard Dosen
- [ ] Buat file `resources/views/dashboard/dosen.blade.php`
- [ ] Update `DashboardController.php` dengan method `dosenDashboard()`
- [ ] Tampilkan statistik:
  - Total materi yang dibuat
  - Total tugas yang dibuat
  - Tugas yang belum dinilai
  - Mahasiswa aktif

### 2. Perbaiki Relationship Models
- [ ] Tambah migration `add_dosen_id_to_materi_table`
- [ ] Tambah migration `add_dosen_id_to_tugas_table`
- [ ] Update model `Materi` dengan relationship `belongsTo(Dosen)`
- [ ] Update model `Tugas` dengan relationship `belongsTo(Dosen)`

### 3. File Upload Validation
- [ ] Tambah validasi di `TugasController@store`
- [ ] Tambah validasi di `MateriController@store`
- [ ] Set max file size (10MB untuk PDF, 50MB untuk video)
- [ ] Allowed extensions: pdf, doc, docx, ppt, pptx, zip, mp4

### 4. Mata Kuliah Views
- [ ] Buat `resources/views/matakuliah/index.blade.php`
- [ ] Buat `resources/views/matakuliah/create.blade.php`
- [ ] Buat `resources/views/matakuliah/edit.blade.php`
- [ ] Tambah menu "Mata Kuliah" di sidebar

---

## 🟡 MEDIUM PRIORITY - 2 Minggu

### 5. Notifications System
- [ ] Buat migration `create_notifications_table`
- [ ] Buat model `Notification`
- [ ] Notifikasi tugas baru (untuk mahasiswa)
- [ ] Notifikasi deadline (H-3, H-1)
- [ ] Notifikasi nilai keluar
- [ ] Badge counter di header

### 6. Peserta/Enrollment Management
- [ ] Buat migration `create_enrollments_table`
- [ ] Buat model `Enrollment`
- [ ] Mahasiswa bisa join mata kuliah
- [ ] Dosen lihat list peserta kelasnya
- [ ] Export list peserta to Excel

### 7. Progress Tracking Enhancement
- [ ] Track completion rate per materi
- [ ] Progress bar di dashboard mahasiswa
- [ ] Certificate generator setelah selesai
- [ ] Achievement badges

### 8. Grading System Enhancement
- [ ] Rubric/kriteria penilaian
- [ ] Comment feedback dari dosen
- [ ] History revisi pengumpulan
- [ ] Appeal/komplain nilai

---

## 🟢 LOW PRIORITY - Future Enhancement

### 9. Attendance System
- [ ] Buat migration `create_attendances_table`
- [ ] QR Code generator untuk check-in
- [ ] Face recognition (optional)
- [ ] Rekap absensi per mahasiswa
- [ ] Export absensi to Excel

### 10. Discussion Forum
- [ ] Buat migration `create_discussions_table`
- [ ] Forum per materi
- [ ] Comment & reply system
- [ ] Like/upvote feature
- [ ] Mark as solved

### 11. Calendar View
- [ ] Integrate FullCalendar.js
- [ ] Show jadwal in calendar format
- [ ] Color-coded by mata kuliah
- [ ] Export to Google Calendar

### 12. Email Notifications
- [ ] Setup mail configuration
- [ ] Email welcome saat register
- [ ] Email tugas baru
- [ ] Email reminder deadline
- [ ] Email nilai keluar

### 13. Export & Reporting
- [ ] Export nilai to Excel (Laravel Excel)
- [ ] Export absensi
- [ ] Generate transcript PDF
- [ ] Statistik pembelajaran per semester

### 14. Live Chat Enhancement
- [ ] Integrate Pusher/Laravel Reverb
- [ ] Real-time chat
- [ ] Group chat per kelas
- [ ] File sharing in chat
- [ ] Typing indicator

### 15. Mobile & PWA
- [ ] Make fully responsive
- [ ] PWA manifest
- [ ] Service worker for offline mode
- [ ] Push notifications
- [ ] Install as app

---

## 🔧 Technical Improvements

### Code Quality
- [ ] Add PHPDoc comments to all methods
- [ ] Implement Form Requests for validation
- [ ] Add unit tests (PHPUnit)
- [ ] Add feature tests for critical flows
- [ ] Setup CI/CD pipeline

### Performance
- [ ] Implement eager loading untuk relationships
- [ ] Add database indexes
- [ ] Cache frequently accessed data
- [ ] Optimize image uploads (compression)
- [ ] Lazy loading untuk large lists

### Security
- [ ] CSRF protection verification
- [ ] XSS protection
- [ ] SQL injection prevention
- [ ] Rate limiting untuk API
- [ ] Two-factor authentication (optional)

---

## 🐛 Known Bugs to Fix

- [ ] Check if role_id is properly saved on user creation
- [ ] Verify sidebar active state on all pages
- [ ] Fix mobile menu toggle
- [ ] Validate file upload in multiple browsers
- [ ] Test timezone consistency

---

## 📝 Documentation Tasks

- [x] Create PROJECT_DOCUMENTATION.md
- [ ] Add API documentation (if applicable)
- [ ] Create user manual (PDF)
- [ ] Add inline code comments
- [ ] Update README.md with screenshots

---

## 🎯 SPRINT PLANNING

### Sprint 1 (Week 1) - Foundation
- Complete all URGENT tasks
- Test migration & seeding
- Verify role-based access control

### Sprint 2 (Week 2) - Dosen Features
- Dashboard Dosen
- Fix relationships
- File upload validation

### Sprint 3 (Week 3) - Engagement Features
- Notifications
- Enrollment system
- Progress tracking

### Sprint 4 (Week 4) - Polish
- Bug fixes
- Performance optimization
- Documentation

---

## 📊 Progress Tracker

```
Total Tasks: 60
Completed: 4 (6.7%)
In Progress: 0
Remaining: 56

URGENT: 2/6 tasks completed (33%)
HIGH: 0/16 tasks completed (0%)
MEDIUM: 0/20 tasks completed (0%)
LOW: 0/18 tasks completed (0%)
```

---

**Last Updated:** 25 Januari 2026

**Notes:**
- Mark completed tasks with [x]
- Update progress weekly
- Prioritize based on user feedback
