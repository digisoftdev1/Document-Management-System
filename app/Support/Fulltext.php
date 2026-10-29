<?php

namespace App\Support;

final class Fulltext
{
    /**
     * Build a safe boolean-mode expression from raw user search input.
     *
     * Returns:
     *   ['match'  => '+tok1 +tok2']  all tokens required (AND semantics)
     *   ['prefix' => 'ab']           raw term for LIKE-prefix fallback when
     *                                no token is long enough for ngram (>= 2 chars)
     *
     * @return array{match?: string, prefix?: string}
     */
    public static function build(string $search): array
    {
        $tokens = collect(preg_split('/\s+/u', trim($search)) ?: [])
            ->map(fn($t) => str_replace(
                ['+', '-', '*', '~', '"', '(', ')', '<', '>', '@'],
                '',
                trim($t)
            ))
            ->filter(fn($t) => mb_strlen($t) >= 2)
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            $raw = trim(preg_replace('/\s+/u', ' ', $search) ?? '');

            return ['prefix' => $raw];
        }

        return ['match' => $tokens->map(fn($t) => '+' . $t)->implode(' ')];
    }

    /**
     * Apply search to a query with a guaranteed-hit guarantee:
     *
     * 1. FULLTEXT (AND semantics across tokens, index-fast at scale)
     * 2. If that yields nothing, indexed prefix-LIKE fallback
     *
     * Covers InnoDB ngram edge cases where valid tokens score rank 0 on
     * small result sets and get filtered out of MATCH-based WHERE clauses.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $base
     * @param array<int, string> $columns
     * @return mixed same builder type, with the search condition applied
     */
    public static function searchClause($base, string $search, array $columns)
    {
        $ft = self::build($search);

        if (isset($ft['match'])) {

            $try = (clone $base)->where(function ($q) use ($ft, $columns) {
                foreach ($columns as $i => $column) {
                    $i === 0
                        ? $q->whereRaw("MATCH({$column}) AGAINST(? IN BOOLEAN MODE)", [$ft['match']])
                        : $q->orWhereRaw("MATCH({$column}) AGAINST(? IN BOOLEAN MODE)", [$ft['match']]);
                }
            });

            if ((clone $try)->limit(1)->exists()) {
                return $try;
            }
        }

        $prefix = $ft['prefix'] ?? trim(preg_replace('/\s+/u', ' ', $search));
        $first = array_shift($columns);

        return (clone $base)->where(function ($q) use ($first, $columns, $prefix) {
            $q->where($first, 'like', $prefix . '%');

            foreach ($columns as $column) {
                $q->orWhere($column, 'like', $prefix . '%');
            }
        });
    }
}
