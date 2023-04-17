<?php

namespace App\Http\Requests;

use App\Models\Shift;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class CalculateEarningsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'arrival' => 'required|date',
            'departure' => 'required|date|after:arrival',
            'bedtime' => 'nullable|date|after:arrival|before:departure',
        ];
    }

    public function calculate(): string
    {
        $bedtime = !empty($this->get('bedtime', null)) ? CarbonImmutable::parse($this->validated('bedtime')) : null;
        $shift = new Shift(...[
            'arrivalTime' => CarbonImmutable::parse($this->validated('arrival')),
            'departureTime' => CarbonImmutable::parse($this->validated('departure')),
            'bedtime' => $bedtime,
        ]);

        return $shift->calculateEarnings();
    }
}
