@php
    $days = ['Hora', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    $hours = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
    $weekStart = \Carbon\Carbon::parse($weeklyCalendar['week_start'] ?? now()->startOfWeek()->toDateString());
    $dates = [];
    for ($i = 0; $i < 7; $i++) {
        $dates[$i] = $weekStart->copy()->addDays($i)->format('Y-m-d');
    }
    $allReservations = [];
    foreach ($courts as $courtData) {
        $courtName = is_object($courtData['court']) ? $courtData['court']->nombre : ($courtData['court']['nombre'] ?? '');
        foreach ($courtData['reservations'] ?? [] as $r) {
            $allReservations[] = array_merge(is_array($r) ? $r : (array) $r, ['court_name' => $courtName]);
        }
    }
@endphp
<table class="w-full border-collapse border border-slate-200 text-sm">
    <thead>
        <tr>
            @foreach($days as $day)
                <th class="border border-slate-200 p-2 bg-slate-50 font-semibold text-slate-700">{{ $day }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($hours as $hour)
            <tr>
                <td class="border border-slate-200 p-2 bg-slate-50 font-medium text-slate-700 whitespace-nowrap">{{ $hour }}</td>
                @for($d = 0; $d < 7; $d++)
                    @php
                        $cellReservations = array_filter($allReservations, function ($r) use ($dates, $d, $hour) {
                            $fecha = is_array($r) ? ($r['fecha'] ?? '') : ($r->fecha ?? '');
                            if (is_object($fecha)) {
                                $fecha = $fecha->format('Y-m-d');
                            }
                            $horaInicio = is_array($r) ? (substr($r['hora_inicio'] ?? '', 0, 5)) : (substr($r->hora_inicio ?? '', 0, 5));
                            return $fecha === $dates[$d] && $horaInicio === $hour;
                        });
                    @endphp
                    <td class="border border-slate-200 p-2 {{ count($cellReservations) > 0 ? 'bg-red-50 border-l-2 border-l-red-400' : 'bg-emerald-50/80 border-l-2 border-l-emerald-400' }}">
                        @foreach($cellReservations as $res)
                            @php
                                $cliente = is_array($res) ? ($res['cliente'] ?? '') : ($res->cliente ?? '');
                                $duracion = is_array($res) ? ($res['duracion_horas'] ?? 0) : ($res->duracion_horas ?? 0);
                                $total = is_array($res) ? ($res['total'] ?? 0) : ($res->total ?? 0);
                                $courtName = $res['court_name'] ?? '';
                            @endphp
                            <div class="text-xs mb-1 p-1.5 bg-red-100 border border-red-200 rounded text-red-800">
                                @if($courtName)<span class="font-medium">{{ $courtName }}</span><br>@endif
                                {{ $cliente }} · {{ $duracion }}h · ${{ number_format((float) $total, 0) }}
                            </div>
                        @endforeach
                    </td>
                @endfor
            </tr>
        @endforeach
    </tbody>
</table>
