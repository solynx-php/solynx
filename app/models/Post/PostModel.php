<?php

namespace app\models\Post;
use app\models\Model;
use app\models\UserModel;

class PostModel extends Model
{
   protected static string $table = 'posts';

   public static function table(): string
    {
      return 'posts';
    }

    public function users(){
      return $this->belongsTo(UserModel::class, 'id', 'user_id');
    }
}
