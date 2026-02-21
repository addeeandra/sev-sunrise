<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'author_id', 'title', 'body'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
