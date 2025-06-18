<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="form-group">
        <label for="current_password">Password Lama</label>
        <input type="password" id="current_password" name="current_password" class="form-control">
        @error('current_password', 'updatePassword')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Password Baru</label>
        <input type="password" id="password" name="password" class="form-control">
        @error('password', 'updatePassword')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Konfirmasi Password Baru</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
        @error('password_confirmation', 'updatePassword')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary">Simpan Password</button>
        @if (session('status') === 'password-updated')
            <span class="text-success ml-3">✔ Password berhasil diperbarui.</span>
        @endif
    </div>
</form>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('status') === 'password-updated')
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Password berhasil diperbarui.',
                timer: 2500,
                showConfirmButton: false
            });
        @endif
    </script>
@endpush
