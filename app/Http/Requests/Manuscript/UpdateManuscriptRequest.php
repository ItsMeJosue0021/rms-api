<?php

namespace App\Http\Requests\Manuscript;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManuscriptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'abstract' => ['sometimes', 'required', 'string'],
            'school_year' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'keywords' => ['sometimes', 'required', 'array'],
            'keywords.*' => ['required_with:keywords', 'string', 'max:255'],
            'authors' => ['sometimes', 'required', 'array'],
            'authors.*' => ['required_with:authors', 'string', 'max:255'],
            'program' => ['sometimes', 'required', 'string', 'max:255'],
            'department' => ['sometimes', 'required', 'string', 'max:255'],
            'files' => ['sometimes', 'array'],
            'files.*' => ['array'],
            'files.*.file_type' => ['nullable', 'string', 'max:100'],
            'files.*.file' => ['required_with:files', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ];
    }
}
