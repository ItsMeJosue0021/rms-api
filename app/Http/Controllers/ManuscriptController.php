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

    public function test() {

        $manuscripts = [
                [
                    'title' => 'Sustainable Campus Energy Optimization',
                    'abstract' => 'A study on reducing electricity and cooling consumption in modern academic buildings.',
                    'school_year' => '2026',
                    'category' => 'Engineering',
                    'keywords' => ['energy', 'sustainability', 'iot'],
                    'authors' => ['Ari Santos', 'Lina Cruz'],
                    'program' => 'Bachelor of Science in Engineering',
                    'department' => 'Mechanical Engineering',
                    'is_public' => true,
                ],
                [
                    'title' => 'Adaptive Learning Patterns in Blended Classrooms',
                    'abstract' => 'Analysis of engagement and outcomes in hybrid learning environments for undergrad classes.',
                    'school_year' => '2025',
                    'category' => 'Education',
                    'keywords' => ['education', 'learning analytics', 'technology'],
                    'authors' => ['Marco Diaz', 'Elena Ruiz'],
                    'program' => 'Bachelor of Science in Computer Science',
                    'department' => 'College of Education',
                    'is_public' => true,
                ],
                [
                    'title' => 'Community-Based Water Monitoring Prototype',
                    'abstract' => 'A low-cost water quality monitoring kit built for local community health awareness initiatives.',
                    'school_year' => '2026',
                    'category' => 'Health Sciences',
                    'keywords' => ['health', 'IoT', 'water quality'],
                    'authors' => ['Nora Villanueva'],
                    'program' => 'Bachelor of Science in Biology',
                    'department' => 'Public Health',
                    'is_public' => false,
                ],
                [
                    'title' => 'Mobile App for Barangay Service Requests',
                    'abstract' => 'Design and implementation of a local governance issue tracking application for citizens.',
                    'school_year' => '2024',
                    'category' => 'Information Technology',
                    'keywords' => ['mobile', 'governance', 'citizen services'],
                    'authors' => ['Miguel Santos'],
                    'program' => 'Bachelor of Science in Information Technology',
                    'department' => 'Information Systems',
                    'is_public' => true,
                ],
                [
                    'title' => 'AI-Assisted Thesis Plagiarism Detection',
                    'abstract' => 'A comparison of text similarity methods and explainable AI for academic integrity systems.',
                    'school_year' => '2026',
                    'category' => 'Computer Science',
                    'keywords' => ['ai', 'nlp', 'plagiarism'],
                    'authors' => ['Rosa Lim', 'Daniel Ong'],
                    'program' => 'Bachelor of Science in Information Technology',
                    'department' => 'Computer Science',
                    'is_public' => false,
                ],
                [
                    'title' => 'Blockchain Voting Integrity Framework',
                    'abstract' => 'A prototype voting platform that tracks audit trails and identity assurance for campus elections.',
                    'school_year' => '2025',
                    'category' => 'Information Systems',
                    'keywords' => ['blockchain', 'security', 'voting'],
                    'authors' => ['Ivy Santos', 'Mark Navarro'],
                    'program' => 'Bachelor of Science in Computer Science',
                    'department' => 'Computer Science',
                    'is_public' => true,
                ],
                [
                    'title' => 'Rural Telemedicine Queue Optimization',
                    'abstract' => 'Modeling appointment routing and triage to reduce waiting time in remote clinics.',
                    'school_year' => '2024',
                    'category' => 'Health Sciences',
                    'keywords' => ['telemedicine', 'health', 'operations research'],
                    'authors' => ['Carla Santos'],
                    'program' => 'Bachelor of Science in Nursing',
                    'department' => 'Medical Sciences',
                    'is_public' => false,
                ],
                [
                    'title' => 'Solar Dryer Design for Agricultural Produce',
                    'abstract' => 'An efficient solar drying system tailored for post-harvest preservation in humid regions.',
                    'school_year' => '2023',
                    'category' => 'Agriculture',
                    'keywords' => ['agriculture', 'renewable energy', 'food safety'],
                    'authors' => ['Ricky De Guzman'],
                    'program' => 'Bachelor of Science in Agriculture',
                    'department' => 'Agricultural Engineering',
                    'is_public' => true,
                ],
                [
                    'title' => 'Smart Parking Availability App',
                    'abstract' => 'A city-focused mobile system for real-time parking occupancy and reservations.',
                    'school_year' => '2026',
                    'category' => 'Urban Technology',
                    'keywords' => ['mobile', 'IoT', 'smart city'],
                    'authors' => ['Jenna Park', 'Leo Cruz'],
                    'program' => 'Bachelor of Science in Information Technology',
                    'department' => 'Civil and Urban Systems',
                    'is_public' => false,
                ],
                [
                    'title' => 'Adaptive Noise Reduction in Classrooms',
                    'abstract' => 'Signal processing techniques to enhance speech clarity in high-noise learning settings.',
                    'school_year' => '2025',
                    'category' => 'Electronics',
                    'keywords' => ['signal processing', 'audio', 'education'],
                    'authors' => ['Noah Reyes'],
                    'program' => 'Bachelor of Science in Electrical Engineering',
                    'department' => 'Electronics Engineering',
                    'is_public' => true,
                ],
            ];

        return response()->json(['manuscripts' => $manuscripts]);
    }

    /**
     * Summary of index
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));
        $manuscripts = $this->manuscriptService->list($perPage, $this->collectListFilters($request, true));

        return ManuscriptResource::collection($manuscripts);
    }

    /**
     * Summary of publicIndex
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function publicIndex(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));
        $manuscripts = $this->manuscriptService->listPublic($perPage, $this->collectListFilters($request));

        return ManuscriptResource::collection($manuscripts);
    }

    private function collectListFilters(Request $request, bool $includePublicFlag = false): array
    {
        $filters = [
            'q' => $request->query('q'),
            'category' => $request->query('category'),
            'program' => $request->query('program'),
            'department' => $request->query('department'),
            'school_year' => $request->query('school_year'),
            'sort' => $request->query('sort'),
            'order' => $request->query('order'),
        ];

        if ($includePublicFlag) {
            $filters['is_public'] = $request->query('is_public');
        }

        return $filters;
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
