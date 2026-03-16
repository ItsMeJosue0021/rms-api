<?php

namespace App\Http\Requests\Manuscript;

use Illuminate\Foundation\Http\FormRequest;

class StoreManuscriptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'abstract' => ['required', 'string'],
            'school_year' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'keywords' => ['required', 'array'],
            'keywords.*' => ['required', 'string', 'max:255'],
            'authors' => ['required', 'array'],
            'authors.*' => ['required', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'files' => ['sometimes', 'array'],
            'files.*' => ['array'],
            'files.*.file_type' => ['nullable', 'string', 'max:100'],
            'files.*.file' => ['required_with:files', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ];
    }
}


//   -H "Authorization: Bearer <token>" \
//   -F "title=Sample Manuscript" \
//   -F "abstract=This is a test abstract." \
//   -F "school_year=2026" \
//   -F "category=Research" \
//   -F "program=Computer Science" \
//   -F "department=Information Technology" \
//   -F "keywords[0]=AI" \
//   -F "keywords[1]=Machine Learning" \
//   -F "authors[0]=Jane Doe" \
//   -F "authors[1]=John Smith" \
//   -F "files[0][file_type]=pdf" \
//   -F "files[0][file]=@/path/to/sample.pdf" \
//   -F "files[1][file_type]=docx" \
//   -F "files[1][file]=@/path/to/sample.docx"
