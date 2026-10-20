@if (session('success'))<div class="flash ok" role="status">{{ session('success') }}</div>@endif
@if (session('error'))<div class="flash bad" role="alert">{{ session('error') }}</div>@endif
@error('cart')<div class="flash bad" role="alert">{{ $message }}</div>@enderror
