<?php
class Worker extends Eloquent {
    
    protected $table = 'workers';
    public $timestamps = false;
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('User');
    }
    public function restorer()
    {
        return $this->belongsTo('Restorer');
    }
    public function orders()
    {
        return $this->hasMany('Order');
    }

}