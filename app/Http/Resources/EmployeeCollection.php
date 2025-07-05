<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_employees' => $this->collection->count(),
                'active_employees' => $this->collection->where('status', 'active')->count(),
                'inactive_employees' => $this->collection->where('status', 'inactive')->count(),
                'departments' => $this->collection->pluck('department.name')->unique()->filter()->values(),
            ],
        ];
    }

    public function with(Request $request): array
    {
        return [
            'links' => [
                'self' => $request->url(),
            ],
        ];
    }
}
