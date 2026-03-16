<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Summary of index
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);

        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 20;
        }

        $query = ActivityLog::query()
            ->with('actor:id,name,email,role')
            ->latest('created_at');

        return ActivityLogResource::collection($query->paginate($perPage));
    }
}

