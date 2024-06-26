<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->getRoleNames()[0] ?? null,
            'email' => $this->email,
            'emailVerifiedAt' => $this->email_verified_at
                ? $this->email_verified_at->toDateTimeString()
                : null,
            'key' => $this->key,
            'createdAt' => $this->created_at->toDateTimeString()
        ];
    }
}
