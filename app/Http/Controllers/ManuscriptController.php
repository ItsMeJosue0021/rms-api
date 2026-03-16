<?php

namespace App\Http\Controllers;

use App\Http\Requests\Manuscript\StoreManuscriptRequest;
use App\Http\Requests\Manuscript\UpdateManuscriptRequest;
use App\Http\Resources\Auth\MessageResource;
use App\Http\Resources\ManuscriptResource;
use App\Models\Manuscript;
use App\Services\ManuscriptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ManuscriptController extends Controller
{
    public function __construct(private readonly ManuscriptService $manuscriptService) {}

    /**
     * Summary of index
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));
        $manuscripts = $this->manuscriptService->list($perPage);

        return ManuscriptResource::collection($manuscripts);
    }

    /**
     * Summary of store
     * @param StoreManuscriptRequest $request
     * @return JsonResponse
     */
    public function store(StoreManuscriptRequest $request): JsonResponse
    {
        $manuscript = $this->manuscriptService->create($request->validated());

        return (new ManuscriptResource($manuscript))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Summary of show
     * @param Manuscript $manuscript
     * @return ManuscriptResource
     */
    public function show(Manuscript $manuscript): ManuscriptResource
    {
        $manuscript = $this->manuscriptService->find($manuscript->getKey());

        return new ManuscriptResource($manuscript);
    }

    /**
     * Summary of update
     * @param UpdateManuscriptRequest $request
     * @param Manuscript $manuscript
     * @return ManuscriptResource
     */
    public function update(UpdateManuscriptRequest $request, Manuscript $manuscript): ManuscriptResource
    {
        $manuscript = $this->manuscriptService->update($manuscript, $request->validated());

        return new ManuscriptResource($manuscript);
    }

    /**
     * Summary of destroy
     * @param Manuscript $manuscript
     * @return JsonResponse
     */
    public function destroy(Manuscript $manuscript): JsonResponse
    {
        $this->manuscriptService->delete($manuscript);

        return (new MessageResource('Manuscript deleted.'))->response()->setStatusCode(200);
    }
}
