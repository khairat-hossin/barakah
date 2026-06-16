<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'investment_id',
    'document_type',
    'file_path',
    'file_name',
    'file_size',
    'mime_type',
    'uploaded_by',
    'verified_by',
    'verified_at',
    'is_public',
    'notes',
])]
class InvestmentDocument extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'is_public' => 'boolean',
        ];
    }

    // Relationships
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Methods
    public function verify(User $user): void
    {
        $this->update([
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);
    }

    public function getDownloadPath(): string
    {
        return Storage::disk('private')->path($this->file_path);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function delete()
    {
        // Delete file from storage
        if (Storage::disk('private')->exists($this->file_path)) {
            Storage::disk('private')->delete($this->file_path);
        }

        return parent::delete();
    }
}
