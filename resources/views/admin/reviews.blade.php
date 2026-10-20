@extends('layouts.admin')
@section('title', 'Ulasan pelanggan')
@section('content')
<div class="table-scroll">
<table>
    <thead><tr><th>Pelanggan</th><th>Bintang</th><th>Komentar</th><th>Tanggal</th><th></th></tr></thead>
    <tbody>
    @forelse ($reviews as $r)
        <tr>
            <td>{{ $r->user->name ?? 'Akun dihapus' }}</td>
            <td class="gold">{{ str_repeat('★', $r->rating) }}<span class="muted">{{ str_repeat('★', 5 - $r->rating) }}</span></td>
            <td>{{ $r->body }}</td>
            <td>{{ $r->created_at->format('d M Y H:i') }}</td>
            <td>
                <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Hapus komentar ini?')">
                    @csrf @method('DELETE')
                    <button class="btn small danger" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="muted">Belum ada komentar.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@if ($reviews->hasPages())
<div class="pager">
    @if ($reviews->previousPageUrl())<a class="btn small ghost" href="{{ $reviews->previousPageUrl() }}">Sebelumnya</a>@endif
    <span class="muted">Halaman {{ $reviews->currentPage() }} dari {{ $reviews->lastPage() }}</span>
    @if ($reviews->nextPageUrl())<a class="btn small ghost" href="{{ $reviews->nextPageUrl() }}">Berikutnya</a>@endif
</div>
@endif
@endsection
