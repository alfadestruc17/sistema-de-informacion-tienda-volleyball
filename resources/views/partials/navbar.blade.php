<nav class="bg-white border-b-2 border-sky-200 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between h-14 items-center">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-sky-700 hover:text-sky-600 transition">
                    Arena Sport C.B
                </a>
                <span class="text-slate-300">|</span>
                @php $role = Auth::user()->role->nombre ?? ''; @endphp
                @if($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="text-slate-600 hover:text-sky-600 text-sm font-medium transition">Dashboard</a>
                    <a href="{{ route('admin.pos.index') }}" class="text-slate-600 hover:text-sky-600 text-sm font-medium transition">POS</a>
                    <a href="{{ route('admin.sales.index') }}" class="text-slate-600 hover:text-sky-600 text-sm font-medium transition">Ventas</a>
                    <a href="{{ route('admin.reservations.index') }}" class="text-slate-600 hover:text-sky-600 text-sm font-medium transition">Reservas</a>
                    <a href="{{ route('admin.products.index') }}" class="text-slate-600 hover:text-sky-600 text-sm font-medium transition">Productos</a>
                    <div class="relative" id="reportes-dropdown">
                        <button type="button" id="reportes-btn" class="text-slate-600 hover:text-sky-600 text-sm font-medium flex items-center gap-1 transition">
                            Reportes
                            <svg id="reportes-chevron" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="reportes-menu" class="absolute left-0 mt-1 w-48 bg-white rounded-lg border border-sky-200 shadow-lg z-20 py-1 hidden">
                            <a href="{{ route('dashboard.export.sales') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-sky-50 transition">Exportar ventas</a>
                            <a href="{{ route('dashboard.export.reservations') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-sky-50 transition">Exportar reservas</a>
                        </div>
                    </div>
                    <script>
                    (function() {
                        var btn = document.getElementById('reportes-btn');
                        var menu = document.getElementById('reportes-menu');
                        var chevron = document.getElementById('reportes-chevron');
                        if (!btn || !menu) return;
                        btn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            var wasHidden = menu.classList.contains('hidden');
                            menu.classList.toggle('hidden', !wasHidden);
                            chevron.style.transform = wasHidden ? 'rotate(180deg)' : '';
                        });
                        document.addEventListener('click', function() {
                            menu.classList.add('hidden');
                            chevron.style.transform = '';
                        });
                    })();
                    </script>
                @elseif($role === 'cajero')
                    <a href="{{ route('pos.index') }}" class="text-slate-600 hover:text-sky-600 text-sm font-medium transition">POS</a>
                @else
                    <a href="{{ route('calendar.index') }}" class="text-slate-600 hover:text-sky-600 text-sm font-medium transition">Reservar cancha</a>
                    <a href="{{ route('client.reservations') }}" class="text-slate-600 hover:text-sky-600 text-sm font-medium transition">Mis reservas</a>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">{{ Auth::user()->nombre }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-slate-600 hover:text-sky-600 hover:bg-sky-50 border border-slate-200 px-3 py-1.5 rounded-lg transition">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
