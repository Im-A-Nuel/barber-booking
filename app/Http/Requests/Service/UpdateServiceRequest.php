<?php

namespace App\Http\Requests\Service;

class UpdateServiceRequest extends StoreServiceRequest
{
    /**
     * Rules are inherited; override if update-specific adjustments required.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return parent::rules();
    }
}
