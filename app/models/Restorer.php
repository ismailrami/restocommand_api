<?php
class Restorer extends Eloquent {
    
    protected $table = 'restorers';
    public $timestamps = false;
    use SoftDeletingTrait;
    protected $dates = ['deleted_at'];
    public function user()
    {
        return $this->belongsTo('User');
    }
    public function products()
    {
        return $this->hasMany('Product');
    }
    public function categorys()
    {
        return $this->hasMany('Category');
    }
    public function areas()
    {
        return $this->hasMany('Area');
    }
    public function options()
    {
        return $this->hasMany('Option');
    }
    public function menus()
    {
        return $this->hasMany('Menu');
    }
    public function tvas()
    {
        return $this->hasMany('Tva');
    }
    public function workers()
    {
        return $this->hasMany('Worker');
    }
    public static $rules = array(
            'name_restaurant' => 'Required|Min:3|Max:20'
        );

}