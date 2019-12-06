<?php

namespace App\Imports;

use App\Division;

use App\Vertical;

use App\Brand;

use App\Unit;

use App\Product;

use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // $division = Division::where('name', $row[2])->first();
        // if(!$division) {
        //     $division = new Division;
        //     $division->name = $row[2];
        //     $division->save();
        // }

        // $vertical = Vertical::where('name', $row[1])->first();
        // if(!$vertical) {
        //     $vertical = new Vertical;
        //     $vertical->division_id = $division->id;
        //     $vertical->name = $row[1];
        //     $vertical->save();
        // }

        // $brand = Brand::where('name', $row[0])->first();
        // if(!$brand) {
        //     $brand = new Brand;
        //     $brand->division_id = $division->id;
        //     $brand->vertical_id = $vertical->id;
        //     $brand->name = $row[0];
        //     $brand->save();
        // }

        // $unit = Unit::where('name', 'piece')->first();

        return new Product([
            // 'division_id' => $division->id,
            // 'vertical_id' => $vertical->id,
            // 'brand_id' => $brand->id,
            // 'unit_id' => $unit ? $unit->id : null,
            // 'sap_code' => $row[6],
            // 'name' => $row[3]
        ]);
    }
}
