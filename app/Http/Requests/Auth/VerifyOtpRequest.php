<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Access is gated by auth + pending-OTP session state via routes/middleware.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $length = (int) config('auth_otp.length', 6);

        return [
            'otp' => ['required', 'string', "digits:{$length}"],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'otp' => preg_replace('/\s+/', '', (string) $this->input('otp')),
        ]);
    }

    public function messages(): array
    {
        $length = (int) config('auth_otp.length', 6);

        return [
            'otp.required' => 'Please enter the verification code.',
            'otp.digits' => "The verification code must be {$length} digits.",
        ];
    }
}
