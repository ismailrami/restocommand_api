<?php
class Table extends Eloquent {
    
    protected $table = 'tables';
    public $timestamps = true;
    use SoftDeletingTrait;
    //protected $dates = ['updated_at'];
    protected $dates = ['deleted_at'];
     public function area()
    {
        return $this->belongsTo('Area');
    }
    public function orders()
    {
        return $this->hasMany('Order');
    }
    public static $rules = array(
            'name' => 'Required',
            'coordinate_x' => 'Required|integer',
            'coordinate_y' => 'Required|integer',
            'width'=>'integer',
            'height'=>'integer',
        );
}