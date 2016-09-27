<?php
class Area extends Eloquent {
    
    protected $table = 'areas';
    public $timestamps = false;
    public function restorer()
    {
        return $this->belongsTo('Restorer');
    }
    public function tables()
    {
        return $this->hasMany('Table');
    }

    public static $rules = array(
            'name' => 'Required|Min:3|Max:60',
        );
}