<?php
class OrderLine extends Eloquent {
    
    protected $table = 'orderlines';
    public $timestamps = true;
    public function product()
    {
        return $this->belongsTo('Product');
    }
     public function order()
    {
        return $this->belongsTo('Order');
    }
     public function selectedOptions()
    {
        return $this->hasMany('SelectedOption');
    }
}