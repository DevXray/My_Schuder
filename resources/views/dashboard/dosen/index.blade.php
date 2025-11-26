<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Dosen - My Schuder</title>
    
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
                    <h1><i class="fas fa-chalkboard-teacher"></i> Kelola Dosen</h1>
                    <p>Daftar semua dosen yang terdaftar</p>
                </div>
                <a href="{{ route('dosen.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Tambah Dosen
                </a>
            </div>
        </section>

        @if(session('success'))
        <div class="alert alert-success" style="margin: 20px; padding: 15px; background: #d4edda; color: #155724; border-radius: 8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <section class="card">
            <div class="card-body" style="padding: 0;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f9fafb;">
                            <tr>
                                <th style="padding: 1rem; text-align: left;">NIDN</th>
                                <th style="padding: 1rem; text-align: left;">Nama</th>
                                <th style="padding: 1rem; text-align: left;">Email</th>
                                <th style="padding: 1rem; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dosens as $dosen)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 1rem;">{{ $dosen->nidn }}</td>
                                <td style="padding: 1rem; font-weight: 600;">{{ $dosen->nama }}</td>
                                <td style="padding: 1rem; color: #6b7280;">{{ $dosen->email }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <a href="{{ route('dosen.edit', $dosen->id) }}" 
                                           style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border-radius: 8px; text-decoration: none; font-size: 0.875rem;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form id="delete-form-{{ $dosen->id }}" 
                                              action="{{ route('dosen.destroy', $dosen->id) }}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    onclick="confirmDelete({{ $dosen->id }}, '{{ addslashes($dosen->nama) }}')" 
                                                    style="padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem;">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" style="padding: 3rem; text-align: center; color: #9ca3af;">
                                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                    Belum ada data dosen
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($dosens->hasPages())
                <div style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                    {{ $dosens->links() }}
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
                title: 'Hapus Dosen?',
                html: `Yakin ingin menghapus dosen <strong>${name}</strong>?`,
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