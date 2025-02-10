<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Tag;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

class TagTest extends TestCase
{
  use RefreshDatabase;

  public $product_data = [
    'product_name' => 'torta alemana',
    'product_price' => 4500,
    'product_short_description' => 'lorem ipsup description piola',
  ];

  /**
   * test crear tag
   * @return void
  */
  public function test_crear_tag()
  {
    $tag = Tag::create(['tag_name' => 'salvado']);

    $this->assertDatabaseHas('tags', ['tag_name' => 'salvado']);
    $this->assertInstanceOf('App\Models\Tag', $tag);
  }

  /**
   * test un tag tiene muchos productos
   * @return void
  */
  public function test_un_tag_tiene_productos()
  {
    $tag = Tag::create(['tag_name' => 'salvado']);
    $product = Product::create($this->product_data);
    $tag->products()->attach($product->id);

    $this->assertDatabaseHas('product_tag', ['product_id' => $product->id, 'tag_id' => $tag->id]);
    $this->assertInstanceOf(BelongsToMany::class, $tag->products());
  }
}
