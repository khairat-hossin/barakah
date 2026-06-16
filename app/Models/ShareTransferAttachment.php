<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['transfer_id', 'file_path', 'file_name', 'uploaded_by'])]
class ShareTransferAttachment extends Model
{
    public $timestamps = false;

    protected $fillable = ['transfer_id', 'file_path', 'file_name', 'uploaded_by', 'created_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(ShareTransfer::class, 'transfer_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
