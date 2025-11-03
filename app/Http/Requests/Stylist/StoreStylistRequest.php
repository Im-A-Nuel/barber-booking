<?php

namespace App\Http\Requests\Stylist;

use Illuminate\Foundation\Http\FormRequest;

class StoreStylistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $stylistId = $this->route('stylist') ? $this->route('stylist')->id : null;

        return [
            'user_id' => [
                'required',
                'exists:users,id',
                'unique:stylists,user_id,' . $stylistId,
            ],
            'specialty' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'is_active' => 'required|boolean',
        ];
    }
}
