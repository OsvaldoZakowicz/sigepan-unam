<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Tag::create(['tag_name' => 'blanco']);
    Tag::create(['tag_name' => 'integral']);
    Tag::create(['tag_name' => 'salado']);
    Tag::create(['tag_name' => 'dulce']);
    Tag::create(['tag_name' => 'con membrillo']);
    Tag::create(['tag_name' => 'con batata']);
    Tag::create(['tag_name' => 'con ricota']);
    Tag::create(['tag_name' => 'con crema pastelera']);
    Tag::create(['tag_name' => 'con dulce de leche']);
  }
}
