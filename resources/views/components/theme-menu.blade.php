<div class="dropdown theme-dropdown">
    <button class="icon-button" type="button" data-bs-toggle="dropdown" data-theme-menu aria-expanded="false" aria-label="Pilih tampilan" title="Pilih tampilan">
        <i class="bi bi-circle-half" aria-hidden="true"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end theme-options">
        <li class="dropdown-header">Tampilan</li>
        @foreach(['system' => ['Otomatis', 'display'], 'light' => ['Terang', 'sun'], 'dark' => ['Gelap', 'moon-stars']] as $value => [$label, $icon])
            <li><button class="dropdown-item" type="button" data-theme-choice="{{ $value }}" aria-pressed="false"><i class="bi bi-{{ $icon }}" aria-hidden="true"></i>{{ $label }}<i class="bi bi-check2 theme-check" aria-hidden="true"></i></button></li>
        @endforeach
    </ul>
</div>
