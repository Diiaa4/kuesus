{{-- $user, $action, $showAddress --}}
<form class="a-card narrow" method="POST" action="{{ $action }}">
    @csrf @method('PUT')
    <h3>Data akun</h3>
    <label for="name">Nama</label>
    <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
    @error('name')<p class="err">{{ $message }}</p>@enderror

    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
    @error('email')<p class="err">{{ $message }}</p>@enderror

    <label for="phone">No. WhatsApp</label>
    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" @required(! $user->isAdmin())>
    @error('phone')<p class="err">{{ $message }}</p>@enderror

    @if ($showAddress)
        <label for="address">Alamat</label>
        <textarea id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
        @error('address')<p class="err">{{ $message }}</p>@enderror
    @endif

    <hr>
    <h3>Ganti kata sandi <small class="muted">(kosongkan bila tidak diganti)</small></h3>
    <label for="current_password">Kata sandi saat ini</label>
    <input id="current_password" type="password" name="current_password" autocomplete="current-password">
    @error('current_password')<p class="err">{{ $message }}</p>@enderror

    <label for="password">Kata sandi baru (min. 8 karakter)</label>
    <input id="password" type="password" name="password" autocomplete="new-password">
    @error('password')<p class="err">{{ $message }}</p>@enderror

    <label for="password_confirmation">Ulangi kata sandi baru</label>
    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">

    <div class="form-actions"><button class="btn" type="submit">Simpan profil</button></div>
</form>
