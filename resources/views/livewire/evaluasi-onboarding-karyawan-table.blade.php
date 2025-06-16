<div>
    <div class="mb-3">
        <label for="tahun">Filter Tahun:</label>
        <select id="tahun" class="form-control w-25 d-inline-block ml-2" wire:model="tahun" wire:change="$refresh">
            <option value="">Semua Tahun</option>
            @foreach ($daftarTahun as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
    </div>
    <table class="table">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Proyeksi Karir</th>
                <th>Nama Mentor</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($idps as $index => $idp)
                <tr class="text-center">
                    <td>{{ $idps->firstItem() + $index }}</td>
                    <td>{{ $idp->proyeksi_karir }}</td>
                    <td>{{ $idp->mentor->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('karyawan.EvaluasiIdp.EvaluasiOnboarding.detail', ['id_idp' => $idp->id_idp]) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-eye mr-1"></i> Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Tidak ada data IDP pada tahun ini.</td>
                </tr>
            @endforelse
        </tbody>

    </table>

    {{ $idps->links() }}
</div>
