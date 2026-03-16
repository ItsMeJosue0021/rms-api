<?php

namespace App\Services;

use App\Models\Manuscript;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ManuscriptService
{
    private const FILE_DISK = 'public';

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Manuscript::query()
            ->with('files')
            ->latest()
            ->paginate($perPage);
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

        $manuscript->files->each(function ($file) {
            if (is_string($file->path) && Storage::disk(self::FILE_DISK)->exists($file->path)) {
                Storage::disk(self::FILE_DISK)->delete($file->path);
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

        return $file->storeAs($directory, $filename, self::FILE_DISK);
    }
}
