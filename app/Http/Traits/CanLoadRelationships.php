<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait CanLoadRelationships
{
    public function loadRelationships(QueryBuilder|EloquentBuilder $for, array $relations = null)
    {
        $relations = $relations ?? $this->relations ?? [];
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

        $query = $for;

        foreach ($includes as $index => $relation) {
            if (!in_array($relation, $relations)) {
                unset($includes[$index]);
            }
        }

        if (!empty($includes)) {
            // laravel Eloquent already handle array in with() -> we don't have to do foreach
            $query->with($includes);
        }

        return $query;
    }
}
