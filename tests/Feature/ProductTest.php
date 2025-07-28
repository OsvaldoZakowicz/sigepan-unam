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
    'product_short_description' => 'lorem ipsup description piola',
    'product_expires_in' => 2,
    'product_in_store' => true,
    'product_image_path' => 'productos/pan.jpg'
  ];

  public $recipe_data = [
    'recipe_title' => 'receta de algo',
    'recipe_yields' => 5,
    'recipe_portions' => 3,
    'recipe_preparation_time' => '01:25:00',
    'recipe_instructions' => 'lorem ipsum recetitus',
    'product_id' => null
  ];

  public $price_data = [
    'product_id' => null,
    'quantity' => 1,
    'price' => 100,
    'description' => 'unidad',
    'is_default' => true,
  ];

  public $stock_data = [
    'product_id' => null,
    'recipe_id' => null,
    'lote_code' => 'abcdefghijkl',
    'quantity_total' => 10,
    'quantity_left' => 10,
    'expired_at' => '2025-07-20 21:26:05',
    'elaborated_at' => '2025-07-20 21:26:05',
  ];

  /**
   * @testCase TC001.
   * @purpose Crear un producto.
   * @expectedResult Se crea un producto en el sistema.
   * @observations Ninguna.
   * @return void
   */
  public function test_crear_producto()
  {
    $product = \App\Models\Product::create($this->product_data);

    $this->assertDatabaseHas('products', $this->product_data);
    $this->assertInstanceOf(\App\Models\Product::class, $product);
  }

  /**
   * @testCase TC002.
   * @purpose Un producto tiene varias etiquetas de clasificacion.
   * @expectedResult Se verifica que el producto tiene etiquetas de clasificacion.
   * @observations Ninguna.
   * @return void
   */
  public function test_un_producto_tiene_tags()
  {
    $product = \App\Models\Product::create($this->product_data);

    $tag = \App\Models\Tag::create(['tag_name' => 'salvado']);
    $product->tags()->attach($tag->id);

    $this->assertDatabaseHas('product_tag', ['product_id' => $product->id, 'tag_id' => $tag->id]);
    $this->assertInstanceOf(BelongsToMany::class, $product->tags());
  }

  /**
   * @testCase TC003.
   * @purpose Un producto tiene varias recetas.
   * @expectedResult Se verifica que el producto tiene recetas asociadas.
   * @observations Una o mas recetas.
   * @return void
   */
  public function test_un_producto_tiene_recetas()
  {
    $product = \App\Models\Product::create($this->product_data);

    $this->recipe_data['product_id'] = $product->id;
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    $this->assertDatabaseHas('recipes', $this->recipe_data);
    $this->assertInstanceOf(HasMany::class, $product->recipes());
  }

  /**
   * @testCase TC004.
   * @purpose Un producto tiene varios precios.
   * @expectedResult Se verifica que el producto tiene precios asociadas.
   * @observations Una o mas precios.
   * @return void
   */
  public function test_un_producto_tiene_precios()
  {
    $product = \App\Models\Product::create($this->product_data);

    $this->price_data['product_id'] = $product->id;
    $price = \App\Models\Price::create($this->price_data);

    $this->assertDatabaseHas('prices', $this->price_data);
    $this->assertInstanceOf(HasMany::class, $product->prices());
  }

  /**
   * @testCase TC005.
   * @purpose Un producto tiene varios stocks.
   * @expectedResult Se verifica que el producto tiene varios stocks asociados.
   * @observations Una o mas lotes de stock.
   * @return void
   */
  public function test_un_producto_tiene_stocks()
  {
    $product = \App\Models\Product::create($this->product_data);

    $this->recipe_data['product_id'] = $product->id;
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    $this->stock_data['product_id'] = $product->id;
    $this->stock_data['recipe_id'] = $recipe->id;
    $stock = \App\Models\Stock::create($this->stock_data);

    $this->assertDatabaseHas('stocks', $this->stock_data);
    $this->assertInstanceOf(HasMany::class, $product->stocks());
  }
}
