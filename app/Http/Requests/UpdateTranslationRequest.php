<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $translationId = $this->route('id');

        return [
            'locale' => ['sometimes', 'string', 'max:5', Rule::in(config('app.supported_locales', ['en', 'fr', 'es']))],
            'key' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('translations', 'key')->ignore($translationId)->where('locale', $this->locale ?? 'en'),
            ],
            'content' => ['sometimes', 'string', 'max:10000'],
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
