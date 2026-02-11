<?php

declare(strict_types=1);

namespace App\Http\Requests\Court;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourtRequest extends FormRequest
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
            'nombre' => ['required', 'string'],
            'descripcion' => ['nullable', 'string'],
            'precio_por_hora' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:activo,inactivo'],
        ];
    }
}
