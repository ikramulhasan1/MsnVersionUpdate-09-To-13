<?php

declare(strict_types=1);

namespace App\Models;

use App\Audit\Enums\AuditStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'url',
        'url_hash',
        'status',
    ];

    /**
     * url_hash is an internal lookup optimization (see AuditRepository),
     * not part of any public contract — hidden defensively in case this
     * model is ever serialized directly.
     */
    protected $hidden = [
        'url_hash',
    ];

    protected $casts = [
        'status' => AuditStatus::class,
    ];

    /**
     * Use the UUID for route model binding instead of the numeric id,
     * so audit URLs never leak internal database ids.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Always (re)computes url_hash from the final url attribute at
     * creation time, mirroring AuditRepository::hashUrl(). This covers
     * every creation path — factory, repository, or a direct
     * Audit::create()/save() — rather than relying on each caller to
     * set it correctly, which is what let AuditFactory's definition()
     * go stale when a test overrode 'url' without recomputing the hash.
     */
    protected static function booted(): void
    {
        self::creating(function (self $audit): void {
            $audit->url_hash = md5($audit->url);
        });
    }
}
