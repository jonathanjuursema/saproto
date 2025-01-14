<?php

namespace App\Models;

use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property string $department
 * @property int $member_id
 * @property string $mail
 * @property string $number
 * @property string $givenname
 * @property string $surname
 * @property bool $found
 * @property Member $member
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class UtAccount extends Model
{
    protected $table = 'ut_accounts';

    #[Override]
    protected function casts(): array
    {
        return [
            'found' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected $fillable = [
        'member_id',
        'department',
        'mail',
        'number',
        'givenname',
        'surname',
        'found',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
