<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'expense_id',
    'file_path',
    'file_name',
    'file_size',
    'mime_type',
    'attachment_type',
    'uploaded_by',
    'created_at',
])]
class ExpenseAttachment extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getDownloadFilename(): string
    {
        return $this->file_name;
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($attachment) {
            if ($attachment->file_path && Storage::disk('private')->exists($attachment->file_path)) {
                Storage::disk('private')->delete($attachment->file_path);
            }
        });
    }
}
