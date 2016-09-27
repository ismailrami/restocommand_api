<?php
class SelectedOption extends Eloquent {
    
    protected $table = 'selectedoptions';
    public $timestamps = false;
    public function option()
    {
        return $this->belongsTo('Option');
    }
     public function orderLine()
    {
        return $this->belongsTo('OrderLine');
    }
}