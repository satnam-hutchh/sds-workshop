<?php

namespace Sds\Workshop\Models;
use Illuminate\Support\Str;
use Sds\Workshop\Exceptions\InvalidAttributeException;
abstract class abstractModel{

    protected $attributes = [];
    protected $fillable = [];
    protected $haveSomeValue = FALSE;

    public function __construct(array $attributes = []){
        $this->fill($attributes);
    }

    public function fetch($key){
        return $this->attributes[$key] ?: FALSE;
    }

    public function has($key){
        return isset($this->attributes[$key]) ?: FALSE;
    }

    public function fill(array $attributes){
        $class = get_called_class();

        foreach($attributes as $key => $value){
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
            $this->attributes[$key] = $value;
        }elseif($this->isFillable($snakeKey)){
            $this->attributes[$snakeKey] = $value;
        }elseif($this->isFillable($lowerKey)){
            $this->attributes[$lowerKey] = $value;
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
        return isset($this->attributes[$key]) || ($this->hasGetMutator($key) && !is_null($this->getAttribute($key)));
    }

    public function __unset($key){
        unset($this->attributes[$key]);
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toArray();
    }

    protected function getAttributeFromArray($key){
        $snakeKey = Str::snake($key);
        $lowerKey = Str::lower($key);
        if(array_key_exists($key, $this->attributes)){
            return $this->attributes[$key];
        }elseif(array_key_exists($snakeKey, $this->attributes)){
            return $this->attributes[$snakeKey];
        }elseif(array_key_exists($lowerKey, $this->attributes)){
            return $this->attributes[$lowerKey];
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
        $attributes = $this->attributes;

        foreach($attributes as $key => $value){
            $attributes[$key] = $this->_toArrayRecursive($value);
        }

        if($fillable){
            foreach($this->fillable as $key){
                $attributes[$key] = $attributes[$key]??null;
            }
        }

        return $attributes;
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

    public static function toFlatArray($array, $prefix = ''){
        $result = [];
        foreach ($array as $key => $value){
            $new_key = ($prefix . (empty($prefix) ? '' : '.') . $key);
            if (is_array($value)){
                $result = array_merge($result, static::toFlatArray($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }
        return $result;
    }

    public function initializeAttribute(string | null $messageString){
        $pattern = "/:.\w*/";
        if(!is_null($messageString) && preg_match_all($pattern, $messageString, $matches)) {
            foreach($matches[0] as $match){
                $key = trim($match,':');
                $this->{$key} ??= "-n/a-";
            }
        }
    }
}
