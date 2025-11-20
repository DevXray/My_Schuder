<!-- resources/views/materi/create.blade.php -->

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Materi - My Schuder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-control:focus {
            outline: none;
            border-color: #4A90E2;
        }
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: #4A90E2;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .icon-option {
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .icon-option:hover, .icon-option.active {
            border-color: #4A90E2;
            background: #f0f7ff;
        }
        .color-options {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            border: 3px solid transparent;
        }
        .color-option.blue { background: #4A90E2; }
        .color-option.orange { background: #FF9500; }
        .color-option.green { background: #34C759; }
        .color-option.active {
            border-color: #333;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="margin-bottom: 24px;">
            <i class="fas fa-plus-circle"></i> Tambah Materi Baru
        </h2>

        @if($errors->any())
            <div style="background: #f8d7da; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li style="color: #721c24;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('materi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Dosen Pengampu *</label>
                <select name="dosen_id" class="form-control" required>
                    <option value="">Pilih Dosen</option>
                    @foreach($dosens as $dosen)
                        <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                            {{ $dosen->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Judul Materi *</label>
                <input type="text" name="judul" class="form-control" 
                       value="{{ old('judul') }}" 
                       placeholder="Contoh: Algoritma & Struktur Data" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" 
                          placeholder="Jelaskan tentang materi ini...">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="form-group">
                <label>Kategori *</label>
                <select name="kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <option value="pemrograman" {{ old('kategori') == 'pemrograman' ? 'selected' : '' }}>Pemrograman</option>
                    <option value="matematika" {{ old('kategori') == 'matematika' ? 'selected' : '' }}>Matematika</option>
                    <option value="database" {{ old('kategori') == 'database' ? 'selected' : '' }}>Database</option>
                    <option value="jaringan" {{ old('kategori') == 'jaringan' ? 'selected' : '' }}>Jaringan</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Jumlah Modul *</label>
                    <input type="number" name="jumlah_modul" class="form-control" 
                           value="{{ old('jumlah_modul', 10) }}" min="1" required>
                </div>

                <div class="form-group">
                    <label>Durasi (Jam) *</label>
                    <input type="number" name="durasi_jam" class="form-control" 
                           value="{{ old('durasi_jam', 8) }}" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label>Icon</label>
                <div class="icon-grid">
                    <div class="icon-option" onclick="selectIcon('fa-code')">
                        <i class="fas fa-code fa-2x"></i>
                    </div>
                    <div class="icon-option" onclick="selectIcon('fa-database')">
                        <i class="fas fa-database fa-2x"></i>
                    </div>
                    <div class="icon-option" onclick="selectIcon('fa-globe')">
                        <i class="fas fa-globe fa-2x"></i>
                    </div>
                    <div class="icon-option" onclick="selectIcon('fa-calculator')">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                    <div class="icon-option" onclick="selectIcon('fa-network-wired')">
                        <i class="fas fa-network-wired fa-2x"></i>
                    </div>
                    <div class="icon-option active" onclick="selectIcon('fa-book')">
                        <i class="fas fa-book fa-2x"></i>
                    </div>
                </div>
                <input type="hidden" name="icon" id="iconInput" value="fa-book">
            </div>

            <div class="form-group">
                <label>Warna</label>
                <div class="color-options">
                    <div class="color-option blue active" onclick="selectColor('blue')"></div>
                    <div class="color-option orange" onclick="selectColor('orange')"></div>
                    <div class="color-option green" onclick="selectColor('green')"></div>
                </div>
                <input type="hidden" name="warna" id="colorInput" value="blue">
            </div>

            <div class="form-group">
                <label>File Materi (PDF, DOC, PPT)</label>
                <input type="file" name="file" class="form-control" 
                       accept=".pdf,.doc,.docx,.ppt,.pptx">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Materi
                </button>
                <a href="{{ route('materi.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function selectIcon(icon) {
            document.querySelectorAll('.icon-option').forEach(el => el.classList.remove('active'));
            event.currentTarget.classList.add('active');
            document.getElementById('iconInput').value = icon;
        }

        function selectColor(color) {
            document.querySelectorAll('.color-option').forEach(el => el.classList.remove('active'));
            event.currentTarget.classList.add('active');
            document.getElementById('colorInput').value = color;
        }
    </script>
</body>
</html>