<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time'   => ['nullable', 'date_format:H:i', 'after:start_time'],
            'remarks'    => ['required', 'string', 'max:255'],
            // start_time, end_time に reqiredにするとデータはあるがなぜかはじかれる
            // 要追及箇所

            'breaks' => ['array'],
            'breaks.*.start_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time', 'before:end_time'],
            'breaks.*.end_time'   => ['nullable', 'date_format:H:i', 'after:breaks.*.start_time', 'before_or_equal:end_time'],
        ];
    }

    public function messages()
    {
        return [
            'start_time.required' => '出勤時間は必須です',
            'end_time.required'   => '退勤時間は必須です',
            'end_time.after'      => '出勤時間もしくは退勤時間が不適切な値です',

            'breaks.*.start_time.after_or_equal' => '休憩時間が不適切な値です',
            'breaks.*.start_time.before'         => '休憩時間が不適切な値です',
            'breaks.*.end_time.after'            => '休憩時間もしくは退勤時間が不適切な値です',
            'breaks.*.end_time.before_or_equal'  => '休憩時間もしくは退勤時間が不適切な値です',

            'remarks.required' => '備考を入力してください',
        ];
    }
}
