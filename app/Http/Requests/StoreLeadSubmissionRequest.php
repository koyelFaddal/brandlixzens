<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLeadSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'company_name' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'captcha' => ['required', 'string'],
            'captcha_key' => ['required', 'uuid'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $key = (string) $this->input('captcha_key', '');
                $captchas = $this->session()->get('lead_captchas', []);
                $expected = (string) ($captchas[$key] ?? '');
                $provided = (string) $this->input('captcha', '');

                unset($captchas[$key]);
                $this->session()->put('lead_captchas', $captchas);

                if ($expected === '' || ! hash_equals(strtolower($expected), strtolower($provided))) {
                    $validator->errors()->add('captcha', 'The CAPTCHA answer is incorrect.');
                }
            },
        ];
    }
}
