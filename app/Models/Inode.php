<?php

namespace App\Models;

use App\Enums\InodeTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['type', 'name'])]
class Inode extends Model
{

    use HasFactory;

    /**
     * Root inode created by the seeder,
     * its ID is always 1
     */
    const ROOT_ID = 1;

    ///////////////////////////////////////////////////////////////////////////
    public function parent(): BelongsTo {
        return $this->belongsTo(self::class);
    }

    ///////////////////////////////////////////////////////////////////////////
    public function children(): HasMany {
        return $this
            ->hasMany(self::class, 'parent_id')
            ->orderBy('type')
            ->orderBy('name');
    }

    ///////////////////////////////////////////////////////////////////////////
    public function casts(): array {
        return [
            'type' => InodeTypeEnum::class,
        ];
    }

    ///////////////////////////////////////////////////////////////////////////
    public function isDirectory(): bool {
        return $this->type === InodeTypeEnum::DIRECTORY;
    }

    ///////////////////////////////////////////////////////////////////////////
    public function isFile(): bool {
        return $this->type === InodeTypeEnum::FILE;
    }

    ///////////////////////////////////////////////////////////////////////////
    public function isRoot(): bool {
        return $this->id === self::ROOT_ID;
    }

}
