<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Menu Item Model.
 *
 * @property int $id
 * @property int|null $parent
 * @property string $menuname
 * @property string|null $url
 * @property int|null $page_id
 * @property int $order
 * @property bool $is_member_only
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Page|null $page
 * @property-read Collection|MenuItem[] $children
 *
 * @method static Builder|MenuItem whereCreatedAt($value)
 * @method static Builder|MenuItem whereId($value)
 * @method static Builder|MenuItem whereIsMemberOnly($value)
 * @method static Builder|MenuItem whereMenuname($value)
 * @method static Builder|MenuItem whereOrder($value)
 * @method static Builder|MenuItem wherePageId($value)
 * @method static Builder|MenuItem whereParent($value)
 * @method static Builder|MenuItem whereUpdatedAt($value)
 * @method static Builder|MenuItem whereUrl($value)
 * @method static Builder|MenuItem newModelQuery()
 * @method static Builder|MenuItem newQuery()
 * @method static Builder|MenuItem query()
 *
 * @mixin Eloquent
 */
class MenuItem extends Model
{
    protected $table = 'menuitems';

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'parent', 'page_id', 'order'];
    protected $appends = ['parsed_url'];

    /** @return BelongsTo */
    public function page()
    {
        return $this->belongsTo('App\Models\Page', 'page_id', 'id');
    }

    /** @return HasMany */
    public function children()
    {
        return $this->hasMany('App\Models\MenuItem', 'parent');
    }

    /** @return string|null */
    public function getUrl() : string | null
    {
        if (substr($this->url, 0, 8) == '(route) ') {
            try {
                return route(substr($this->url, 8));
            } catch (Exception $e) {
                return '#';
            }
        } else {
            return $this->url;
        }
    }

    /** @return string|null */
    public function getParsedUrlAttribute() : string | null
    {
        return $this->getUrl();
    }

    /** @return bool */
    public function isFirst()
    {
        $lowest = self::where('parent', '=', $this->parent)->orderBy('order')->first();

        return $this->id == $lowest->id;
    }

    /** @return bool */
    public function isLast()
    {
        $highest = self::where('parent', '=', $this->parent)->orderBy('order', 'desc')->first();

        return $this->id == $highest->id;
    }
}
