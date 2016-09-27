<?php
class Option extends Eloquent {
    
    protected $table = 'options';
    public $timestamps = false;
    use SoftDeletingTrait;
    protected $dates = ['deleted_at'];
    public function restorer()
    {
        return $this->belongsTo('Restorer');
    }
    public function products()
    {
        return $this->belongsToMany('Product');
    }
    public function selectedOptions()
    {
        return $this->hasMany('SelectedOption');
    }

    public static $rules = array(
            'name' => 'Required|Min:3|Max:20|Alpha',
            'values' => 'Required|Alpha',
        );
}