<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'end_time' => 'nullable|after:start_time',
            'break_start_time' => 'array',
            'break_start_time.*' => 'nullable|after:start_time|before:end_time',
            'break_end_time' => 'array',
            'break_end_time.*' => 'nullable|before:end_time',
            'reason' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_start_time.*.after' => '休憩時間が不適切な値です',
            'break_start_time.*.before' => '休憩時間が不適切な値です',
            'break_end_time.*.before' => '休憩時間もしくは退勤時間が不適切な値です',
            'reason.required' => '備考を記入してください'
        ];
    }
}
