<?php

namespace Bloglyzer\Models;

use Bloglyzer\Models\BaseModel;

class OriginalPost extends BaseModel {

	protected $table = 'original_post';

	protected $guarded = ['id'];

	protected $hidden = ['id'];

}