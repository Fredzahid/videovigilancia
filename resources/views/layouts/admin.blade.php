@extends('layouts.app')

@section('styles')
<style>
    .admin-wrapper {
        display: flex;
        min-height: calc(100vh - 72px);
    }

    .sidebar-dashboard {
        width: 260px;
        background: white;
        box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        padding: 20px 0;
        flex-shrink: 0;
    }

    .sidebar-dashboard .user-info {
        padding: 0 20px 20px;
        border-bottom: 1px solid #eee;
        margin-bottom: 20px;
    }

    .sidebar-dashboard .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-green);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 10px;
    }

    .admin-main-content {
        flex: 1;
        min-width: 0;
        padding: 30px;
        background: #f8f9fa;
    }

    @media (max-width: 767.98px) {
        .admin-wrapper {
            display: block;
        }

        .sidebar-dashboard {
            width: 100%;
            padding: 12px 0;
        }

        .sidebar-dashboard .user-info {
            display: none;
        }

        .sidebar-dashboard nav {
            display: flex;
            overflow-x: auto;
            padding: 0 12px;
        }

        .sidebar-dashboard .sidebar-link {
            flex: 0 0 auto;
            margin: 0 4px;
        }

        .admin-main-content {
            padding: 20px 15px;
        }
    }
</style>
@yield('admin-styles')
@endsection

@section('content')
<div class="admin-wrapper">
    <aside class="sidebar-dashboard">
        <div class="user-info text-center">
            <div class="user-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <h6 class="mb-0">{{ auth()->user()->name ?? 'Usuario' }}</h6>
            <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
        </div>

        <nav>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Panel Principal
            </a>
            <a href="{{ route('news.index') }}" class="sidebar-link {{ request()->routeIs('news.*') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i> Noticias
            </a>
            <a href="{{ route('advertisements.index') }}" class="sidebar-link {{ request()->routeIs('advertisements.*') ? 'active' : '' }}">
                <i class="bi bi-image"></i> Publicidad
            </a>
            <a href="{{ route('header.edit') }}" class="sidebar-link {{ request()->routeIs('header.*') ? 'active' : '' }}">
                <i class="bi bi-layout-text-window"></i> Encabezado
            </a>
            <a href="{{ route('pages.edit') }}" class="sidebar-link {{ request()->routeIs('pages.edit') || request()->routeIs('pages.update') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Contáctenos / Nosotros
            </a>
            <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Usuarios
            </a>
            <a href="{{ route('sections.index') }}" class="sidebar-link {{ request()->routeIs('sections.*') ? 'active' : '' }}">
                <i class="bi bi-collection"></i> Secciones
            </a>
            <a href="{{ route('surveillance.index') }}" class="sidebar-link {{ request()->routeIs('surveillance.*') ? 'active' : '' }}">
                <i class="bi bi-camera-video"></i> Videovigilancia
            </a>
        </nav>
    </aside>

    <div class="admin-main-content">
        @yield('admin-content')
    </div>
</div>
@endsection
