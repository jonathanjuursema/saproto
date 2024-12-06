<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

/**
 * Validatable Model.
 *
 * @method static Builder|Validatable newModelQuery()
 * @method static Builder|Validatable newQuery()
 * @method static Builder|Validatable query()
 */
class Validatable extends Model
{
    protected array $rules = [];

    protected MessageBag $errors;

    public function validate($data): bool
    {
        $v = Validator::make($data, $this->rules);
        if ($v->fails()) {
            $this->errors = $v->errors();

            return false;
        }

        return true;
    }

    public function errors(): MessageBag
    {
        return $this->errors;
    }
}
