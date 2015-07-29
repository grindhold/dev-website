<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
  protected $fillable = ['title', 'slug', 'tags', 'author', 'content'];

  public function author()
  {
    return $this->belongsTo(User::class, 'author_id', 'id');
  }

  public function tags()
  {
    return $this->belongsToMany(Tag::class);
  }

  public function comments()
  {
    return $this->hasMany(Comment::class);
  }
}
