<?php
class Order extends Eloquent {
    
    protected $table = 'orders';
    public $timestamps = true;
    public function orderLines()
    {
        return $this->hasMany('OrderLine');
    }
     public function table()
    {
        return $this->belongsTo('Table');
    }
    public function worker()
    {
        return $this->belongsTo('Worker');
    }

    public function cachings()
    {
        return $this->hasMany('Caching');
    }
    public static $rules = array(

        );
}