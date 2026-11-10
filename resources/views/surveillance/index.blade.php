@extends('layouts.admin')

@section('title', 'Videovigilancia')

@section('admin-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4><i class="bi bi-camera-video"></i> Sistema de Videovigilancia</h4>
            <p class="text-muted mb-0">Monitoreo de las cámaras registradas.</p>
        </div>
        <a href="{{ route('surveillance.cameras.create') }}" class="btn btn-gold flex-shrink-0">
            <i class="bi bi-plus-circle"></i> Agregar cámara
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            Revisa los datos del formulario e inténtalo de nuevo.
        </div>
    @endif

    <div class="alert alert-info" role="note">
        Para cámaras RTSP, configura la pasarela MediaMTX indicada en el README y guarda aquí la URL HLS resultante.
        No ingreses la contraseña de la cámara en esta página.
    </div>

    <div class="row g-4">
        @forelse($cameras as $camera)
            <div class="col-12 col-md-6 col-xl-4">
                <article class="card h-100 shadow-sm">
                    <div class="camera-preview bg-dark text-white d-flex align-items-center justify-content-center">
                        @if($camera->stream_url)
                            <video class="w-100 h-100" data-hls-url="{{ $camera->stream_url }}" controls playsinline muted>
                                El navegador no puede reproducir esta transmisión.
                            </video>
                        @else
                            <div class="text-center p-4">
                                <i class="bi bi-camera-video-off display-4"></i>
                                <p class="mb-0 mt-2">Transmisión no configurada</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h5 class="card-title mb-2">{{ $camera->name }}</h5>
                            @php
                                $statusClass = match ($camera->status) {
                                    'online' => 'success',
                                    'maintenance' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($camera->status) }}</span>
                        </div>
                        @if($camera->location)
                            <p class="text-muted mb-2"><i class="bi bi-geo-alt"></i> {{ $camera->location }}</p>
                        @endif
                        @if($camera->description)
                            <p class="card-text mb-0">{{ $camera->description }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-end gap-2">
                        <a href="{{ route('surveillance.cameras.edit', $camera) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form action="{{ route('surveillance.cameras.destroy', $camera) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar esta cámara?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center shadow-sm">
                    <div class="card-body py-5">
                        <i class="bi bi-camera-video display-4 text-muted"></i>
                        <p class="mt-3 mb-0">Todavía no hay cámaras registradas.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('admin-styles')
<style>
    .camera-preview {
        aspect-ratio: 16 / 9;
        overflow: hidden;
    }

    .camera-preview video {
        object-fit: contain;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@1"></script>
<script>
    document.querySelectorAll('video[data-hls-url]').forEach((video) => {
        const source = video.dataset.hlsUrl;

        if (video.canPlayType('application/vnd.apple.mpegurl')) {
            video.src = source;
        } else if (window.Hls && Hls.isSupported()) {
            const player = new Hls();
            player.loadSource(source);
            player.attachMedia(video);
        } else {
            video.insertAdjacentHTML('afterend', '<p class="text-white text-center p-3">Este navegador no admite HLS.</p>');
        }
    });
</script>
@endsection
