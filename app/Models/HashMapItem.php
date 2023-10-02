<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * HashMap Item Model.
 *
 * @property int $id
 * @property string $key
 * @property string|null $subkey
 * @property string $value
 *
 * @method static Builder|HashMapItem key($key)
 * @method static Builder|HashMapItem subkey($subkey)
 * @method static Builder|HashMapItem whereId($value)
 * @method static Builder|HashMapItem whereKey($value)
 * @method static Builder|HashMapItem whereSubkey($value)
 * @method static Builder|HashMapItem whereValue($value)
 * @method static builder|hashMapItem newmodelquery()
 * @method static builder|hashMapItem newquery()
 * @method static builder|hashMapItem query()
 *
 * @mixin Eloquent
 */
class HashMapItem extends Model
{
    use HasFactory;

    protected $table = 'hashmap';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * @param  Builder  $query
     * @param  string  $key
     * @return Builder
     */
    public function scopeKey($query, $key)
    {
        return $query->where('key', '=', $key);
    }

    /**
     * @param  Builder  $query
     * @param  string  $subkey
     * @return Builder
     */
    public function scopeSubkey($query, $subkey)
    {
        return $query->where('subkey', '=', $subkey);
    }
}
