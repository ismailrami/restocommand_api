<?php
class Category extends Eloquent {
    
    protected $table = 'categorys';
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

    public function isParent($query)
    {
        return $query->where('category_id', '=', null);
    }
    public function children()
    {
        return $this->hasMany('Category', 'category_id'); 
    }
    public function parent()
    {
        if($this->category_id !== null && $this->category_id> 0)
        {
            return $this->belongsTo('Category','category_id');
        } 
        else 
        {
            return null;
        }
    }

    public static $rules = array(
            'name' => 'Required|Min:3',
            'position' => 'Required|integer',
        );
}