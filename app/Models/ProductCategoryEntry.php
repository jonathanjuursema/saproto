<?php

namespace App\Models;

use Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Product Category Entry Model.
 *
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ProductCategory $ProductCategory
 * @property-read Product $product
 *
 * @method static Builder|ProductCategoryEntry whereCategoryId($value)
 * @method static Builder|ProductCategoryEntry whereCreatedAt($value)
 * @method static Builder|ProductCategoryEntry whereId($value)
 * @method static Builder|ProductCategoryEntry whereProductId($value)
 * @method static Builder|ProductCategoryEntry whereUpdatedAt($value)
 * @method static Builder|ProductCategoryEntry newModelQuery()
 * @method static Builder|ProductCategoryEntry newQuery()
 * @method static Builder|ProductCategoryEntry query()
 *
 * @mixin Eloquent
 */
class ProductCategoryEntry extends Model
{
    protected $table = 'products_categories';

    protected $guarded = ['id'];

    protected $rules = [
        'user_id' => 'required|integer',
        'achievement_id' => 'required|integer',
        'rank' => 'required|integer',
    ];

    /** @return BelongsTo */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo */
    public function ProductCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
