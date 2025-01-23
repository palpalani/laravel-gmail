<?php

declare(strict_types=1);

namespace Dacastro4\LaravelGmail\Traits;

use Illuminate\Support\Arr;

trait SendsParameters
{
    /**
     * Adds values to the property which is used to send additional parameters in the request.
     *
     * @param $query
     * @param string $column
     * @param bool $encode
     */
    public function add($query, $column = 'q', $encode = true): void
    {
        $query = $encode ? urlencode($query) : $query;

        if (isset($this->params[$column])) {
            if ('pageToken' === $column) {
                $this->params[$column] = $query;
            } else {
                $this->params[$column] = "{$this->params[$column]} {$query}";
            }
        } else {
            $this->params = Arr::add($this->params, $column, $query);
        }

    }

    public function addPageToken($token): void
    {
        $this->params['pageToken'] = $token;
    }
}
