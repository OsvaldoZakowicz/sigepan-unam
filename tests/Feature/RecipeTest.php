<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecipeTest extends TestCase
{
  use RefreshDatabase;

  public $recipe_data = [
    'recipe_title'              =>  'pan',
    'recipe_yields'             =>  10,
    'recipe_portions'           =>  8,
    'recipe_preparation_time'   =>  '00:25:00', //HH:mm:ss
    'recipe_instructions'       =>  'lorem ipsum',
    'recipe_short_description'  =>  'lorem ipsum'
  ];

  /**
   * * crear receta
   */
  public function test_crear_receta()
  {
    DB::table('recipes')->insert($this->recipe_data);

    $this->assertDatabaseHas('recipes', $this->recipe_data);
  }

  /**
   * * existe el modelo de receta
   */
  public function test_existe_modelo_receta()
  {
    $recipe = \App\Models\Recipe::create($this->recipe_data);

    $this->assertInstanceOf(\App\Models\Recipe::class, $recipe);
  }

}
