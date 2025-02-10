<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use App\Models\Tag;
use Tests\TestCase;

class ProductTest extends TestCase
{
  use RefreshDatabase;

  public $product_data = [
    'product_name' => 'torta alemana',
    'product_price' => 4500,
    'product_short_description' => 'lorem ipsup description piola',
  ];

  /**
   * test crear un producto
   * @return void
  */
  public function test_crear_producto()
  {
    $product = Product::create($this->product_data);

    $this->assertDatabaseHas('products', $this->product_data);
    $this->assertInstanceOf('App\Models\Product', $product);
  }

  /**
   * test un producto tiene varias tags
   * @return void
  */
  public function test_un_producto_tiene_tags()
  {
    $product = Product::create($this->product_data);
    $tag = Tag::create(['tag_name' => 'salvado']);
    $product->tags()->attach($tag->id);

    $this->assertDatabaseHas('product_tag', ['product_id' => $product->id, 'tag_id' => $tag->id]);
    $this->assertInstanceOf(BelongsToMany::class, $product->tags());
  }
}
