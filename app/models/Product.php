<?php
class Product extends Eloquent {
    
    protected $table = 'products';
    public $timestamps = false;
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    public function restorer()
    {
        return $this->belongsTo('Restorer');
    }
     public function steps()
    {
        return $this->belongsToMany('Step');
    }
    public function options()
    {
        return $this->belongsToMany('Option');
    }
    public function tva()
    {
        return $this->belongsTo('Tva');
    }
    public function pictures()
    {
        return $this->hasMany('Picture');
    }
    public function category()
    {
        return $this->belongsTo('Category');
    }
    public function orderLines()
    {
        return $this->hasMany('OrderLine');
    }
}