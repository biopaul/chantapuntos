<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:-10', 'max:10'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'points' => 'puntaje',
        ];
    }
}
