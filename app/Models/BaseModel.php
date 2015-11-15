<?php

namespace Bloglyzer\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class BaseModel extends Eloquent {

	protected $connection = 'mongodb';

}