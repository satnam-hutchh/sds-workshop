<?php

namespace Sds\Workshop\Builders;

use Sds\Workshop\Traits\ValidatesFields;
use Illuminate\Support\Str;

abstract class RequestBuilder
{
    use ValidatesFields;

    protected array $rules = [];
    public string $method;
    public string $endpoint;
    public array $headers = [];
    public array $query = [];
    public bool $authRequest = false;
    protected $body = [];
    protected $fillable = [];
    protected $haveSomeValue = FALSE;

    public function __construct(array $body = []){
        $this->fill($body);
    }

    public static function make(string $method, string $endpoint): static
    {
        $obj = new static();
        $obj->method = $method;
        $obj->endpoint = $endpoint;
        return $obj;
    }

    public function withHeaders(array $headers): static
    {
        $this->headers = $headers;
        return $this;
    }

    public function withBody(array $body): static
    {
        $this->fill($body);
        return $this;
    }

    public function validate(): void
    {
        $this->validateFields($this->body, $this->rules);
    }

    public function toPayload(): array
    {
        $this->validate();
        return $this->body;
    }

    public function fetch($key){
        return $this->body[$key] ?: FALSE;
    }

    public function has($key){
        return isset($this->body[$key]) ?: FALSE;
    }

    public function fill(array $body){
        $class = get_called_class();

        foreach($body as $key => $value){
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    public function isFillable($key){
        if(in_array($key, $this->fillable)){
            return TRUE;
        }

        return empty($this->fillable);
    }

    public function getAttribute($key){
        $value = $this->getAttributeFromArray($key);
        return $value;
    }

    public function setAttribute($key, $value){
        if($this->hasSetMutator($key)){
            $method = 'set' . $this->getStudlyCase($key) . 'Attribute';
            $this->haveSomeValue = TRUE;

            return $this->{$method}($value);
        }
        $snakeKey = Str::snake($key);
        $lowerKey = Str::lower($key);
        if($this->isFillable($key)){
            $this->body[$key] = $value;
        }elseif($this->isFillable($snakeKey)){
            $this->body[$snakeKey] = $value;
        }elseif($this->isFillable($lowerKey)){
            $this->body[$lowerKey] = $value;
        }

        return $this;
    }

    public function hasGetMutator($key){
        return method_exists($this, 'get' . $this->getStudlyCase($key) . 'Attribute');
    }

    public function hasSetMutator($key){
        return method_exists($this, 'set' . $this->getStudlyCase($key) . 'Attribute');
    }

    public function __get($key){
        return $this->getAttribute($key);
    }

    public function __set($key, $value){
        $this->setAttribute($key, $value);
    }

    public function __isset($key){
        return isset($this->body[$key]) || ($this->hasGetMutator($key) && !is_null($this->getAttribute($key)));
    }

    public function __unset($key){
        unset($this->body[$key]);
    }
    
    public function __toString()
    {
        return $this->toArray();
    }

    protected function getAttributeFromArray($key){
        $snakeKey = Str::snake($key);
        $lowerKey = Str::lower($key);
        if(array_key_exists($key, $this->body)){
            return $this->body[$key];
        }elseif(array_key_exists($snakeKey, $this->body)){
            return $this->body[$snakeKey];
        }elseif(array_key_exists($lowerKey, $this->body)){
            return $this->body[$lowerKey];
        }else{
            if($this->hasGetMutator($key)){
                $method = 'get' . $this->getStudlyCase($key) . 'Attribute';
                return $this->{$method}();
            }
        }

        throw new InvalidAttributeException(sprintf("Undefined property '%s' in class '%s'", $key, get_called_class()));

        return NULL;
    }

    protected function getStudlyCase($str){
        return ucfirst(Str::studly($str));
    }

    public function toArray(bool $fillable = false){
        return $this->attributesToArray($fillable);
    }

    public function toCollection(bool $fillable = false){
        return $this->attributesToArray($fillable);
    }

    public function attributesToArray(bool $fillable = false){
        $body = $this->body;

        foreach($body as $key => $value){
            $body[$key] = $this->_toArrayRecursive($value);
        }

        if($fillable){
            foreach($this->fillable as $key){
                $body[$key] = $body[$key]??null;
            }
        }

        return $body;
    }

    private function _toArrayRecursive($subject){
        if(is_array($subject)){
            foreach($subject as $key => $value){
                $subject[$key] = $this->_toArrayRecursive($value);
            }

            return $subject;
        }

        return $subject instanceof Arrayable ? $subject->toArray() : $subject;
    }
}