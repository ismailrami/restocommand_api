<?php
class Picture extends Eloquent {
    
    protected $table = 'pictures';
    public $timestamps = false;
    public function product()
    {
        return $this->belongsTo('Product');
    }
}