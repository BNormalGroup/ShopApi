<?php

namespace App\Http\Requests\Likes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:items,id',
            'user_id' => [
                'required',
                'exists:users,id',
                // Додавання правила для унікальності комбінації item_id та user_id
                Rule::unique('likes')->where(function ($query) {
                    return $query->where('user_id', $this->user_id)
                        ->where('item_id', $this->item_id);
                }),
            ],
        ];
    }
}
