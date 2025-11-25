<?php

namespace Sds\Workshop\Models;

class Token extends BaseModel
{
    public string $access_token;
    public ?string $refresh_token = null;
    public int $expires_in;
}
