<?php

declare(strict_types=1);

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'court_id' => ['required', 'exists:courts,id'],
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'duracion_horas' => ['required', 'integer', 'min:1', 'max:8'],
            'estado' => ['required', 'in:pendiente,confirmada,cancelada'],
            'pagado_bool' => ['nullable', 'boolean'],
        ];
    }
}
