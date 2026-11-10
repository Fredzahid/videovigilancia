<form action="{{ $action }}" method="POST">
    @csrf
    @if(isset($camera))
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Nombre de la cámara</label>
        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $camera->name ?? '') }}" required maxlength="255">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="location" class="form-label">Ubicación</label>
        <input id="location" name="location" type="text" class="form-control @error('location') is-invalid @enderror"
               value="{{ old('location', $camera->location ?? '') }}" maxlength="255">
        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="stream_url" class="form-label">URL de transmisión HLS</label>
        <input id="stream_url" name="stream_url" type="url"
               class="form-control @error('stream_url') is-invalid @enderror"
               value="{{ old('stream_url', $camera->stream_url ?? '') }}"
               placeholder="http://localhost:8888/camara/index.m3u8" maxlength="2048">
        <div class="form-text">
            Pega la dirección HLS del navegador. Si la cámara es RTSP, consulta la configuración de MediaMTX en el README.
        </div>
        @error('stream_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Estado</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(['offline' => 'Desconectada', 'online' => 'En línea', 'maintenance' => 'Mantenimiento'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $camera->status ?? 'offline') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <div class="form-text">El estado se selecciona manualmente; la aplicación no detecta automáticamente si la cámara responde.</div>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Descripción</label>
        <textarea id="description" name="description" rows="3"
                  class="form-control @error('description') is-invalid @enderror"
                  maxlength="2000">{{ old('description', $camera->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('surveillance.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-gold">{{ $buttonText }}</button>
    </div>
</form>
