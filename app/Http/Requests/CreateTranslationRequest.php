<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', 'max:5', Rule::in(config('app.supported_locales', ['en', 'fr', 'es']))],
            'key' => ['required', 'string', 'max:255', 'unique:translations,key,NULL,id,locale,'.$this->locale],
            'content' => ['required', 'string', 'max:10000'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'locale.in' => 'The selected locale is not supported.',
            'key.unique' => 'A translation with this key already exists for the specified locale.',
        ];
    }
}
