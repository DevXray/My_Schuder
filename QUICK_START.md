# 🚀 Quick Start Guide - My Schuder

## Langkah Cepat Setup Project

### 1️⃣ Jalankan Migration & Seeding
```bash
# Buka terminal di folder project
cd d:\xampp\htdocs\My_Schuder

# Hapus database lama dan buat ulang dengan data baru
php artisan migrate:fresh --seed
```

**Output yang diharapkan:**
```
✅ Roles seeded successfully with IDs: 1=admin, 2=dosen, 3=mahasiswa
✅ Admin user created: admin@schuder.ac.id / admin123
✅ 2 Dosen users created: dosen123
✅ 5 Mahasiswa users created: mahasiswa123
✅ Database seeded successfully!

=== LOGIN CREDENTIALS ===
Admin: admin@schuder.ac.id / admin123
Dosen: budi.santoso@schuder.ac.id / dosen123
Mahasiswa: mahasiswa1@student.schuder.ac.id / mahasiswa123
```

---

### 2️⃣ Test Login untuk 3 Role

#### Test Admin
1. Buka browser: `http://localhost/My_Schuder/public` atau `http://127.0.0.1:8000`
2. Login dengan:
   - Email: `admin@schuder.ac.id`
   - Password: `admin123`
3. **Yang harus terlihat:**
   - Dashboard Admin dengan statistik
   - Sidebar menu: Users, Mahasiswa, Dosen, Mata Kuliah
   - Role badge: "Administrator"

#### Test Dosen
1. Logout dari admin
2. Login dengan:
   - Email: `budi.santoso@schuder.ac.id`
   - Password: `dosen123`
3. **Yang harus terlihat:**
   - Dashboard Student (sementara, nanti dibuat dashboard dosen)
   - Sidebar menu: Materi, Tugas, Jadwal
   - Role badge: "Dosen"
   - Bisa create/edit/delete materi & tugas

#### Test Mahasiswa
1. Logout dari dosen
2. Login dengan:
   - Email: `mahasiswa1@student.schuder.ac.id`
   - Password: `mahasiswa123`
3. **Yang harus terlihat:**
   - Dashboard Student
   - Sidebar menu: Materi, Tugas, Jadwal
   - Role badge: "Mahasiswa"
   - TIDAK bisa create/edit materi & tugas (hanya view)

---

### 3️⃣ Verifikasi Middleware Protection

#### Test 1: Mahasiswa Coba Akses Admin Route
```
1. Login sebagai mahasiswa
2. Manual akses: http://localhost/My_Schuder/public/users
3. HASIL: Harus redirect ke dashboard dengan error "Unauthorized"
```

#### Test 2: Dosen Coba Akses Admin Route
```
1. Login sebagai dosen
2. Manual akses: http://localhost/My_Schuder/public/users
3. HASIL: Harus redirect ke dashboard dengan error "Unauthorized"
```

#### Test 3: Admin Akses Semua Route
```
1. Login sebagai admin
2. Coba akses:
   - /users ✅
   - /mahasiswa ✅
   - /dosen ✅
   - /materi ✅
   - /tugas ✅
3. HASIL: Semua harus bisa diakses
```

---

## 🔧 Troubleshooting

### Problem: Migration Error "Table 'roles' already exists"
**Solusi:**
```bash
# Drop semua table dan buat ulang
php artisan migrate:fresh --seed
```

### Problem: Login Gagal / Credential Invalid
**Solusi:**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Coba lagi
```

### Problem: Role Badge Tidak Muncul
**Check:**
1. Pastikan user punya `role_id`
   ```sql
   SELECT id, name, email, role_id FROM users;
   ```
2. Pastikan relationship User-Role ada
   ```bash
   php artisan tinker
   >>> $user = User::find(1);
   >>> $user->role;
   >>> $user->getRoleName();
   ```

### Problem: Middleware Tidak Jalan
**Check:**
1. File: `app/Http/Kernel.php`
2. Pastikan ada:
   ```php
   'role' => \App\Http\Middleware\CheckRole::class,
   ```
3. Restart server:
   ```bash
   php artisan serve
   ```

---

## 📝 Next Steps

Setelah semua test berhasil, lanjut ke prioritas berikutnya:

### Immediate (Hari Ini)
1. ✅ Verify migration & seeding works
2. ✅ Test login 3 roles
3. ✅ Verify middleware protection
4. ⏳ Buat view Dosen Dashboard

### This Week
1. ⏳ Fix Dosen-Materi-Tugas relationships
2. ⏳ Add file upload validation
3. ⏳ Complete Mata Kuliah views
4. ⏳ Add notifications system

---

## 🎯 Checklist Sebelum Development Lanjut

- [ ] Database seeded successfully
- [ ] Admin login works
- [ ] Dosen login works
- [ ] Mahasiswa login works
- [ ] Middleware blocks unauthorized access
- [ ] All navigation links work
- [ ] No console errors (F12 developer tools)
- [ ] Mobile responsive works

**Jika semua ✅, Anda siap lanjut development!**

---

## 📞 Need Help?

Jika ada error:
1. Check `storage/logs/laravel.log`
2. Check browser console (F12)
3. Run `php artisan about` untuk info environment

---

**Created:** 25 Januari 2026
**Status:** Ready for Development ✨
