<?php
class Tva extends Eloquent {
    
    protected $table = 'tvas';
    public $timestamps = false;
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
    public function restorer()
    {
        return $this->belongsTo('Restorer');
    }
    public function products()
    {
        return $this->hasMany('Product');
    }
    public static $rules = array(
            'name' => 'Required|Min:3|Max:20|Alpha',
            'value' => 'Required|numeric',
        );
}