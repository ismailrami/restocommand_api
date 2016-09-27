<?php
class Menu extends Eloquent {
    
    protected $table = 'menus';
    public $timestamps = false;
    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
    public function restorer()
    {
        return $this->belongsTo('Restorer');
    }
    public function steps()
    {
        return $this->hasMany('Step');
    }
    public static $rules = array(
            'name' => 'Required|Min:3|Max:20',
        );
}