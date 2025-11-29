<?php

namespace Sds\Workshop\Builders\Vehicle;

use Sds\Workshop\Builders\RequestBuilder;

class CreateRequest extends RequestBuilder
{
    protected string $method = 'POST';
    protected string $endpoint = 'resources';

    protected array $rules = [
        'name'        => ['required', 'string'],
        'description' => ['string'],
        'metadata'    => ['array'],
    ];

    public function name(string $value): static
    {
        $this->data['name'] = $value;
        return $this;
    }

    public function description(string $value): static
    {
        $this->data['description'] = $value;
        return $this;
    }

    public function metadata(array $value): static
    {
        $this->data['metadata'] = $value;
        return $this;
    }
}
