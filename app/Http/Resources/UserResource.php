<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\BranchResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'image' => $this->image ? url(Storage::url($this->image)) : null,
            'status' => $this->status,
            // 'roles' => $this->getRoleNames() ?? null,
            // 'permissions' => $this->getAllPermissions()->pluck('name') ?? null,
            'created_at' => $this->created_at?->format('M d, Y h:i A') ?? null,
            'updated_at' => $this->updated_at?->format('M d, Y h:i A') ?? null,
        ];
    }
}
