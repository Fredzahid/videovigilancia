<?php

namespace Database\Seeders;

use App\Models\Camera;
use Illuminate\Database\Seeder;

class CameraSeeder extends Seeder
{
    public function run(): void
    {
        $cameras = [
            [
                'name' => 'Entrada principal',
                'location' => 'Acceso principal',
                'rtsp_url' => 'rtsp://192.0.2.10:554/stream1',
                'description' => 'Cámara de prueba. Sustituye la URL por la dirección RTSP real.',
            ],
            [
                'name' => 'Redacción',
                'location' => 'Área de redacción',
                'rtsp_url' => 'rtsp://192.0.2.11:554/stream1',
                'description' => 'Cámara de prueba. Sustituye la URL por la dirección RTSP real.',
            ],
            [
                'name' => 'Estacionamiento',
                'location' => 'Zona de estacionamiento',
                'rtsp_url' => 'rtsp://192.0.2.12:554/stream1',
                'description' => 'Cámara de prueba. Sustituye la URL por la dirección RTSP real.',
            ],
        ];

        foreach ($cameras as $camera) {
            Camera::updateOrCreate(
                ['name' => $camera['name']],
                [...$camera, 'status' => 'offline']
            );
        }
    }
}
