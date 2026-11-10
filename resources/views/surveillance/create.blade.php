@extends('layouts.admin')

@section('title', 'Agregar cámara')

@section('admin-content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-soft-green">
                    <h5 class="mb-0"><i class="bi bi-camera-video"></i> Agregar cámara</h5>
                </div>
                <div class="card-body">
                    @include('surveillance._form', [
                        'action' => route('surveillance.cameras.store'),
                        'buttonText' => 'Guardar cámara',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
