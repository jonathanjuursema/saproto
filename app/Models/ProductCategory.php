<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Override;

/**
 * Product Category Model.
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @method static QueryBuilder|ProductCategory onlyTrashed()
 * @method static QueryBuilder|ProductCategory withTrashed()
 * @method static QueryBuilder|ProductCategory withoutTrashed()
 * @method static Builder|ProductCategory whereCreatedAt($value)
 * @method static Builder|ProductCategory whereDeletedAt($value)
 * @method static Builder|ProductCategory whereId($value)
 * @method static Builder|ProductCategory whereName($value)
 * @method static Builder|ProductCategory whereUpdatedAt($value)
 * @method static Builder|ProductCategory newModelQuery()
 * @method static Builder|ProductCategory newQuery()
 * @method static Builder|ProductCategory query()
 *
 * @mixin Eloquent
 */
class ProductCategory extends Model
{
    use SoftDeletes;

    protected $table = 'product_categories';

    protected $guarded = ['id'];

    /** @return BelongsToMany */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'products_categories', 'category_id', 'product_id');
    }

    public function sortedProducts()
    {
        $products = $this->belongsToMany(Product::class, 'products_categories', 'category_id', 'product_id')->get();

        return $products->sortBy('name');
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }
}
