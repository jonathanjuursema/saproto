<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\MessageBag;
use Validator;

/**
 * Validatable Model.
 *
 * @method static Builder|Validatable newModelQuery()
 * @method static Builder|Validatable newQuery()
 * @method static Builder|Validatable query()
 *
 * @mixin Eloquent
 */
class Validatable extends Eloquent
{
    protected $rules = [];

    protected $errors;

    /** @return bool */
    public function validate($data)
    {
        $v = Validator::make($data, $this->rules);
        if ($v->fails()) {
            $this->errors = $v->errors();

            return false;
        }

        return true;
    }

    /** @return MessageBag */
    public function errors()
    {
        return $this->errors;
    }
}
