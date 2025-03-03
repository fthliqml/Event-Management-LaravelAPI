<?php

namespace App\Http\Traits;

trait HasValidationRules
{
    public function eventRules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ];
    }
}
