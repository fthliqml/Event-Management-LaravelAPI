<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanLoadRelationships
{
    public function loadRelationships(QueryBuilder|EloquentBuilder|HasMany|Model $for, array $relations = [])
    {
        $relations = empty($relations) ? $this->relations : $relations;
        // change the params (string) and returning an array -> explode. And delete space in string -> str_replace
        // array filter -> deleting item if its ""
        $includes = array_filter(
            explode(
                ',',
                str_replace(
                    ' ',
                    '',
                    request()->query('include')
                )
            )
        );


        foreach ($includes as $index => $relation) {
            if (!in_array($relation, $relations)) {
                unset($includes[$index]);
            }
        }

        if (!empty($includes)) {
            $for instanceof Model ?
                $for->load($includes) :
                $for->with($includes);

        }

        return $for;
    }
}
