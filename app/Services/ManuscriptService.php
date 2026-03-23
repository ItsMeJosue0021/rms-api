<?php

namespace App\Services;

use App\Models\Manuscript;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ManuscriptService
{
    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Manuscript::query()
            ->with('files')
            ;

        $this->applyListFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function listPublic(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Manuscript::query()
            ->with('files')
            ->where('is_public', true)
            ;

        $this->applyListFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function find(string $id): Manuscript
    {
        return Manuscript::query()
            ->with('files')
            ->findOrFail($id);
    }

    public function create(array $payload): Manuscript
    {
        $files = $payload['files'] ?? [];
        unset($payload['files']);

        $manuscript = Manuscript::query()->create($payload);
        $this->replaceFiles($manuscript, $files);

        return $this->find($manuscript->getKey());
    }

    public function update(Manuscript $manuscript, array $payload): Manuscript
    {
        $files = array_key_exists('files', $payload) ? $payload['files'] : null;
        unset($payload['files']);

        $manuscript->update($payload);

        if ($files !== null) {
            $this->replaceFiles($manuscript, $files);
        }

        return $this->find($manuscript->getKey());
    }

    public function delete(Manuscript $manuscript): bool
    {
        $this->deleteManuscriptFiles($manuscript);

        return (bool) $manuscript->delete();
    }

    private function applyListFilters(Builder $query, array $filters): void
    {
        $keyword = isset($filters['q']) ? trim((string) $filters['q']) : '';

        if ($keyword !== '') {
            $like = '%' . $keyword . '%';
            $query->where(function (Builder $q) use ($like): void {
                $q->where('title', 'like', $like)
                    ->orWhere('abstract', 'like', $like);
            });
        }

        foreach (['category', 'program', 'department', 'school_year'] as $field) {
            if (isset($filters[$field]) && $filters[$field] !== '') {
                $query->where($field, (string) $filters[$field]);
            }
        }

        if (array_key_exists('is_public', $filters) && $filters['is_public'] !== null && $filters['is_public'] !== '') {
            $query->where('is_public', $this->normalizeBoolean($filters['is_public']));
        }

        $sort = (string) ($filters['sort'] ?? '');
        $order = strtolower((string) ($filters['order'] ?? 'desc'));
        $allowedSorts = ['title', 'school_year', 'category', 'program', 'department', 'created_at', 'updated_at'];

        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $order === 'asc' ? 'asc' : 'desc');
            return;
        }

        $query->latest();
    }

    private function normalizeBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
        }

        return (bool) $value;
    }

    private function replaceFiles(Manuscript $manuscript, array $files): void
    {
        $this->deleteManuscriptFiles($manuscript);

        if (empty($files)) {
            return;
        }

        $prepared = $this->prepareFilePayloads($manuscript, $files);
        $manuscript->files()->createMany($prepared);
    }

    private function deleteManuscriptFiles(Manuscript $manuscript): void
    {
        $manuscript->loadMissing('files');
        $disk = $this->disk();

        $manuscript->files->each(function ($file) {
            if (is_string($file->path) && $disk->exists($file->path)) {
                $disk->delete($file->path);
            }
        });

        $manuscript->files()->delete();
    }

    private function prepareFilePayloads(Manuscript $manuscript, array $files): array
    {
        return array_values(array_filter(array_map(
            fn(array $file): ?array => $this->buildFilePayload($manuscript, $file),
            $files
        )));
    }

    private function buildFilePayload(Manuscript $manuscript, array $fileData): ?array
    {
        if (! isset($fileData['file']) || ! $fileData['file'] instanceof UploadedFile) {
            return null;
        }

        return [
            'file_type' => $this->resolveFileType($fileData),
            'path' => $this->storeUploadedFile($manuscript->getKey(), $fileData['file']),
        ];
    }

    private function resolveFileType(array $fileData): string
    {
        if (! empty($fileData['file_type'])) {
            return (string) $fileData['file_type'];
        }

        return (string) strtolower(pathinfo($fileData['file']->getClientOriginalName(), PATHINFO_EXTENSION));
    }

    private function storeUploadedFile(string $manuscriptId, UploadedFile $file): string
    {
        $directory = "manuscripts/{$manuscriptId}";
        $filename = sprintf('%s_%s.%s',
            Str::uuid()->toString(),
            now()->format('YmdHis'),
            $file->getClientOriginalExtension()
        );

        return $file->storeAs($directory, $filename, $this->diskName());
    }

    private function diskName(): string
    {
        return (string) config('filesystems.manuscripts_disk', 'public');
    }

    private function disk(): FilesystemAdapter
    {
        return Storage::disk($this->diskName());
    }
}
