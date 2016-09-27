<?php
class Caching extends Eloquent {
    
    protected $table = 'cachings';
    public $timestamps = true;

    public function order()
    {
        return $this->belongsTo('Order');
    }
}