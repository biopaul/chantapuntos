<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Controllers\ChildController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChildRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('child')->user_id;
    }

    public function rules(): array
    {
        $validIcons = array_column(ChildController::availableIcons(), 'id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', Rule::in($validIcons)],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'icon' => 'ícono',
            'avatar' => 'foto',
        ];
    }
}
