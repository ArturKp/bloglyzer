<?php

namespace Bloglyzer\Models;

use Bloglyzer\Models\BaseModel;

class Emotionalword extends BaseModel {

	protected $table = 'emotionalwords';

	protected $guarded = ['id'];

	protected $hidden = ['id'];

}