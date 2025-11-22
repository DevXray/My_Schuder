{{-- resources/views/administrator/mahasiswa/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Mahasiswa - My Schuder</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/app.js'])
</head>
<body>
    @include('partials.header')
    @include('partials.sidebar')

    <main class="main-content" id="mainContent">
        <section class="page-header">
            <div class="page-header-content">
                <div class="page-title-section">
                    <h1><i class="fas fa-user-graduate"></i> Kelola Mahasiswa</h1>
                    <p>Daftar semua mahasiswa yang terdaftar</p>
                </div>
                <a href="{{ route('administrator.mahasiswa.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Tambah Mahasiswa
                </a>
            </div>
        </section>

        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <!-- Table -->
        <section class="card">
            <div class="card-body" style="padding: 0;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f9fafb;">
                            <tr>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">NIM</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Nama</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Email</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Jurusan</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Kelas</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mahasiswas as $mhs)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 1rem;">{{ $mhs->nim }}</td>
                                <td style="padding: 1rem; font-weight: 600;">{{ $mhs->nama }}</td>
                                <td style="padding: 1rem; color: #6b7280;">{{ $mhs->email }}</td>
                                <td style="padding: 1rem;">{{ $mhs->jurusan ?? '-' }}</td>
                                <td style="padding: 1rem;">{{ $mhs->kelas ?? '-' }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <a href="{{ route('administrator.mahasiswa.edit', $mhs->id) }}" 
                                           style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border-radius: 8px; text-decoration: none; font-size: 0.875rem;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button onclick="confirmDelete({{ $mhs->id }}, '{{ $mhs->nama }}')" 
                                                style="padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem;">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                        <form id="delete-form-{{ $mhs->id }}" action="{{ route('administrator.mahasiswa.destroy', $mhs->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="padding: 3rem; text-align: center; color: #9ca3af;">
                                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                    Belum ada data mahasiswa
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($mahasiswas->hasPages())
                <div style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                    {{ $mahasiswas->links() }}
                </div>
                @endif
            </div>
        </section>
    </main>

    @include('partials.chatbot')
    <div class="overlay" id="overlay"></div>

    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus Mahasiswa?',
                html: `Yakin ingin menghapus mahasiswa <strong>${name}</strong>?<br>Data tidak dapat dikembalikan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</body>
</html>