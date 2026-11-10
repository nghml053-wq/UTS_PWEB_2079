@foreach(['success' => 'check-circle', 'info' => 'info-circle', 'error' => 'exclamation-circle'] as $type => $icon)
    @if(session($type))
        <div class="alert notice notice-{{ $type }} alert-dismissible fade show" role="{{ $type === 'error' ? 'alert' : 'status' }}">
            <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
            <div>{{ session($type) }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup pemberitahuan"></button>
        </div>
    @endif
@endforeach

@if($errors->any())
    <div class="alert notice notice-error" role="alert">
        <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
        <div><strong>Periksa kembali data yang dimasukkan.</strong><ul class="validation-summary">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    </div>
@endif
