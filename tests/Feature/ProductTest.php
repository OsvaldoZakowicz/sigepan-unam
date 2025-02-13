<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class ProductTest extends TestCase
{
  use RefreshDatabase;

  public $product_data = [
    'product_name' => 'torta alemana',
    'product_price' => 4500,
    'product_short_description' => 'lorem ipsup description piola',
    'product_expires_in' => 2,
    'product_in_store' => true,
  ];

  public $recipe_data = [
    'recipe_title' => 'receta de algo',
    'recipe_yields' => 5,
    'recipe_portions' => 3,
    'recipe_preparation_time' => '01:25:00',
    'recipe_instructions' => 'lorem ipsum recetitus',
    'product_id' => ''
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

  /**
   * test un producto tiene varias recetas
   * @return void
  */
  public function test_un_producto_tiene_recetas()
  {
    $product = Product::create($this->product_data);
    $this->recipe_data['product_id'] = $product->id;
    $recipe = Recipe::create($this->recipe_data);

    $this->assertDatabaseHas('recipes', $this->recipe_data);
    $this->assertInstanceOf(HasMany::class, $product->recipes());
  }
}
