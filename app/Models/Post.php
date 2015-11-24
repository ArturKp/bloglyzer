<?php

namespace Bloglyzer\Models;

use Bloglyzer\Models\BaseModel;

class Post extends BaseModel {

	protected $table = 'post';

	// protected $dates = ['date'];

	protected $guarded = ['id'];

	protected $hidden = ['id', 'html'];

}