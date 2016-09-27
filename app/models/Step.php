<?php
class Step extends Eloquent {
    
    protected $table = 'steps';
    public $timestamps = false;
    public function menu()
    {
        return $this->belongsTo('Menu');
    }
     public function products()
    {
        return $this->belongsToMany('Product');
    }
}