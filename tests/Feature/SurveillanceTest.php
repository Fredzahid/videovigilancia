<?php

namespace Tests\Feature;

use App\Models\Camera;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveillanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_surveillance_page_requires_authentication(): void
    {
        $this->get(route('surveillance.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_and_play_registered_hls_cameras(): void
    {
        $camera = Camera::create([
            'name' => 'Entrada principal',
            'stream_url' => 'http://localhost:8888/camara/index.m3u8',
            'location' => 'Acceso principal',
            'status' => 'online',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('surveillance.index'))
            ->assertOk()
            ->assertSee($camera->name)
            ->assertSee($camera->location)
            ->assertSee('Sistema de Videovigilancia')
            ->assertSee('data-hls-url="http://localhost:8888/camara/index.m3u8"', false)
            ->assertSee('hls.js');
    }

    public function test_authenticated_user_can_register_and_delete_a_camera(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('surveillance.cameras.create'))
            ->assertOk()
            ->assertSee('URL de transmisión HLS');

        $this->post(route('surveillance.cameras.store'), [
            'name' => 'Cámara del patio',
            'location' => 'Patio',
            'stream_url' => 'http://localhost:8888/camara/index.m3u8',
            'status' => 'offline',
            'description' => 'Prueba',
        ])
            ->assertRedirect(route('surveillance.index'));

        $camera = Camera::where('name', 'Cámara del patio')->firstOrFail();

        $this->assertSame('http://localhost:8888/camara/index.m3u8', $camera->stream_url);

        $this->get(route('surveillance.cameras.edit', $camera))
            ->assertOk()
            ->assertSee('Editar cámara');

        $this->put(route('surveillance.cameras.update', $camera), [
            'name' => 'Cámara actualizada',
            'location' => 'Nuevo patio',
            'stream_url' => 'https://streams.example.test/camara.m3u8',
            'status' => 'online',
            'description' => 'Actualizada',
        ])->assertRedirect(route('surveillance.index'));

        $this->assertDatabaseHas('cameras', [
            'id' => $camera->id,
            'name' => 'Cámara actualizada',
            'status' => 'online',
        ]);

        $this->delete(route('surveillance.cameras.destroy', $camera))
            ->assertRedirect(route('surveillance.index'));

        $this->assertDatabaseMissing('cameras', ['id' => $camera->id]);
    }

    public function test_camera_stream_url_must_use_http_or_https(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('surveillance.cameras.create'))
            ->post(route('surveillance.cameras.store'), [
                'name' => 'Cámara RTSP',
                'stream_url' => 'rtsp://camera.local/stream',
                'status' => 'offline',
            ])
            ->assertRedirect(route('surveillance.cameras.create'))
            ->assertSessionHasErrors('stream_url');
    }
}
