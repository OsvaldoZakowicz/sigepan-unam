<?php

namespace Database\Seeders;

use App\Models\ProvisionType;
use App\Models\Measure;
use App\Models\ProvisionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvisionCategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // obtener tipos de suministro
    $tipo_ingrediente = ProvisionType::where('provision_type_name', 'ingrediente')->first();
    $tipo_insumo      = ProvisionType::where('provision_type_name', 'insumo')->first();

    // obtener unidades de medida
    $medida_kg  = Measure::where('unit_name', 'kilogramo')->first();
    $medida_lts = Measure::where('unit_name', 'litro')->first();
    $medida_un  = Measure::where('unit_name', 'unidad')->first();
    $medida_mt  = Measure::where('unit_name', 'metro')->first();

    // categorias de suministro basicas
    // provision_category_id_editable = false, por defecto en la tabla

    // ----------------------TIPO INGREDIENTE-----------------------------

    ProvisionCategory::create([
      'provision_category_name' => 'harina 000',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'harina 0000',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'azucar',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'maizena',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'harina leudante',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'aceite mezcla',
      'measure_id'              => $medida_lts->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'aceite girasol',
      'measure_id'              => $medida_lts->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'leche entera',
      'measure_id'              => $medida_lts->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'leche descremada',
      'measure_id'              => $medida_lts->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'levadura humeda',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'levadura seca',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'polvo para hornear',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'azucar inpalpable',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'dulce de membrillo',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'dulce de batata',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'dulce de leche',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'ricota',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'huevo',
      'measure_id'              => $medida_un->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'crema de leche',
      'measure_id'              => $medida_kg->id,
      'provision_type_id'       => $tipo_ingrediente->id
    ]);

    // -----------------------TIPO INSUMOS----------------------------

    ProvisionCategory::create([
      'provision_category_name' => 'bolsa plastica numero 10',
      'measure_id'              => $medida_un->id,
      'provision_type_id'       => $tipo_insumo->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'bolsa de papel numero 10',
      'measure_id'              => $medida_un->id,
      'provision_type_id'       => $tipo_insumo->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'bandeja de carton numero 12',
      'measure_id'              => $medida_un->id,
      'provision_type_id'       => $tipo_insumo->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'bandeja de carton numero 16',
      'measure_id'              => $medida_un->id,
      'provision_type_id'       => $tipo_insumo->id
    ]);

    ProvisionCategory::create([
      'provision_category_name' => 'plastico envoltorio',
      'measure_id'              => $medida_mt->id,
      'provision_type_id'       => $tipo_insumo->id
    ]);

  }
}
