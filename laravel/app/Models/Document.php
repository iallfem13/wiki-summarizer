<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['original_content', 'filename', 'slug', 'status'];

    protected $casts = [
        'processed_at' => 'datetime',
        'tech_stacks' => 'array', // cast to array so it's more practical. When accessing, you can get it as array
        'status' => DocumentStatus::class,
    ];

    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', DocumentStatus::Pending);
    }

    #[Scope]
    protected function processing(Builder $query): void
    {
        $query->where('status', DocumentStatus::Processing);
    }

    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->where('status', DocumentStatus::Completed);
    }

    #[Scope]
    protected function failed(Builder $query): void
    {
        $query->where('status', DocumentStatus::Failed);
    }
}
