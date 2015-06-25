<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model {

    /**
     * Fillable fields for a provider.
     * @var array
     */
	protected $fillable = [
      'name',
      'copyright_email',
    ];

}
