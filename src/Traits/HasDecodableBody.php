<?php

declare(strict_types=1);

namespace Dacastro4\LaravelGmail\Traits;

trait HasDecodableBody
{
    /**
     * @param $content
     *
     * @return string
     */
    public function getDecodedBody($content)
    {
        $content = str_replace('_', '/', str_replace('-', '+', $content));

        return base64_decode($content);
    }

}
