<?php

declare(strict_types=1);

namespace Dacastro4\LaravelGmail\Traits;

trait HasHeaders
{
    abstract public function getHeaders();

    /**
     * Gets a single header from an existing email by name.
     *
     * @param $headerName
     *
     * @param string $regex if this is set, value will be evaluated with the give regular expression.
     *
     * @return null|string
     */
    public function getHeader($headerName, $regex = null)
    {
        $headers = $this->getHeaders();

        $value = null;

        foreach ($headers as $header) {
            if ($header->key === $headerName) {
                $value = $header->value;
                if (null !== $regex) {
                    preg_match_all($regex, $header->value, $value);
                }
                break;
            }
        }

        if (is_array($value)) {
            return $value[1] ?? null;
        }

        return $value;
    }

}
