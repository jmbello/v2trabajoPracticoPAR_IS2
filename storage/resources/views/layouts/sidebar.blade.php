<!-- resources/views/layouts/sidebar.blade.php -->
@auth
<div class="bg-dark text-white p-3" style="min-width: 250px; height: 100vh;">
    <h5 class="text-white">🧾 Nómina</h5>
    <hr>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Cerrar sesión</button>
    </form>
    <hr>
    {{ Auth::user()->getRoleNames() }}
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link text-white">Inicio</a>
        </li>

        {{-- Empleados --}}
        @if(in_array(Auth::user()->role, ['Administrador', 'Gerente', 'Encargado_rrhh']))
            <li class="nav-item">
                <a href="#empleadosSubmenu" class="nav-link text-white" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="empleadosSubmenu">
                    👥 Empleados
                </a>
                <ul id="empleadosSubmenu" class="collapse ps-3">
                    <li><a href="{{ route('personas.index') }}" class="nav-link text-white">CRUD Personasyyyyy</a></li>
                    {{-- Puedes agregar más opciones aquí --}}
                    <li><a href="{{ route('empleados.index') }}" class="nav-link text-white">CRUD Empleados</a></li>
                </ul>
            </li>
        @endif

        {{-- Administración (Solo para administradores) --}}
        @if(Auth::user()->role === 'administrador')
            {{-- <li class="nav-item"><a href="#" class="nav-link text-white">Configuración</a></li> --}}
        @endif
    </ul>
</div>
@endauth
