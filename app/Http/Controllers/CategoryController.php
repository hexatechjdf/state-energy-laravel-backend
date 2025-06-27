<?php

namespace App\Http\Controllers;

use App\Models\Adder;
use App\Models\Category;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{

    public function index()
    {
        return view('admin.category.index');
    }
    public function getTableData(Request $req)
    {
        $items = Category::query();

        // Apply search filtering for the category columns
        if (!empty($req->search['value'])) {
            $searchValue = $req->search['value'];
            $items->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('adders', 'like', "%{$searchValue}%");
            });
        }

        return DataTables::eloquent($items)
            ->addColumn('thumbnail', function ($item) {
                return $item->thumbnail_url
                    ? '<img class="table-image" src="' . $item->thumbnail_url . '" height="50"/>'
                    : '-';
            })
            ->addColumn('detail_photo', function ($item) {
                return $item->detail_photo_url
                    ? '<img class="table-image" src="' . $item->detail_photo_url . '" height="50"/>'
                    : '-';
            })
            ->addColumn('adders', function ($item) {
                return $item->adders->pluck('name')->implode(', ');
            })
            ->addColumn('action', function ($item) {
                return '<button type="button" class="btn btn-sm btn-primary btn-edit-category"
                data-id="' . $item->id . '">
                <i class="fas fa-edit"></i> Edit
            </button>';
            })
            ->rawColumns(['thumbnail', 'detail_photo', 'adders', 'action'])
            ->make(true);
    }
    public function show($id)
    {
        $category = Category::with('adders')->findOrFail($id);
        return response()->json([
            'category' => $category
        ]);
    }
    public function update1(Request $request)
    {
        $category = Category::findOrFail($request->category_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'thumbnail' => 'nullable|image',
            'detail_photo' => 'nullable|image',
        ]);

        $category->name = $request->name;

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $category->thumbnail = $path;
        }

        if ($request->hasFile('detail_photo')) {
            $path = $request->file('detail_photo')->store('detail_photos', 'public');
            $category->detail_photo = $path;
        }

        $category->save();

        $adderIds = [];
        if ($request->adders) {
            foreach ($request->adders as $adderName) {
                $adder = Adder::firstOrCreate(['name' => $adderName]);
                $adderIds[] = $adder->id;
            }
        }

        $category->adders()->sync($adderIds);

        return response()->json(['message' => 'Category updated successfully.']);
    }
    public function update(Request $request, FileUploadService $fileService)
    {
        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:255',
            'adders_names'  => 'array',
            'adders_prices' => 'array',
            'thumbnail'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'detail_photo'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $category = Category::findOrFail($request->category_id);

        // Update image fields
        if ($request->hasFile('thumbnail')) {
            if ($category->thumbnail) {
                $fileService->delete($category->thumbnail, 'public');
            }
            $category->thumbnail = $fileService->upload(
                $request->file('thumbnail'),
                'category/thumbnails',
                'public'
            );
        }

        if ($request->hasFile('detail_photo')) {
            if ($category->detail_photo) {
                $fileService->delete($category->detail_photo, 'public');
            }
            $category->detail_photo = $fileService->upload(
                $request->file('detail_photo'),
                'category/detail_photos',
                'public'
            );
        }

        $category->name = $request->input('name');
        $category->save();

        // Update configuration & pricing
        $existingConfig = json_decode($category->configuration, true);
        $updatedFields  = [];
        $newPricing     = [];
        $index          = 0;
        $finalPricingOptions = [];
        $categoryOriginalPricing = json_decode($category->pricing);
        foreach ($existingConfig['fields'] as $field) {
            if (in_array($field['type'], ['select', 'dynamic_select'])) {
                $fieldOptions = $request->input("config_fields.$index.options");
                $fieldPricing = $request->input("config_fields.$index.pricing");

                if ($fieldOptions) {
                   
                    if (is_array($fieldOptions) && array_keys($fieldOptions) !== range(0, count($fieldOptions) - 1)) {
                        // dynamic_select with subcategories
                        dd("dd");
                        foreach ($fieldOptions as $subCat => $options) {
                            foreach ($options as $option) {
                                if (isset($field['pricing']) && $field['pricing'] === 'true') {
                                    $newPricing[$subCat][$option] = $fieldPricing[$subCat][$option] ?? null;
                                }
                            }
                        }
                        $field['options'] = $fieldOptions;
                    } else {
                        // regular select
                        foreach ($fieldOptions as $option) {
                            if (isset($field['pricing']) && $field['pricing'] === 'true') {
                                if ($category->name == 'Roof') {
                                    $newPricing[$option] = [
                                        'price_per_sqft' => $fieldPricing[$option] ?? null
                                    ];
                                }
                                if ($category->name == 'Solar') {
                                    $newPricing['price_per_watt'] = $categoryOriginalPricing->price_per_watt;
                                    $newPricing['battery'][$option] = $fieldPricing[$option] ?? null;
                                }
                                if ($category->name == 'HVAC' || $category->name == 'Insulation') {
                                    $newPricing[$option] = $fieldPricing[$option] ?? null;
                                }
                                if ($category->name == 'Doors') {
                                     $newPricing[$option] = [
                                        'price' => $fieldPricing[$option] ?? null
                                    ];
                                }
                                if ($category->name == 'Insulation') {
                                      $newPricing[$option] = $fieldPricing[$option] ?? null;
                                }
                            }
                        }
                        $field['options'] = $fieldOptions;
                    }
                }
                $updatedFields[] = $field;
                $index++;
            } else {
                $updatedFields[] = $field;
            }
        }
        if($category->name == 'Windows'){
            $newPricing  = $categoryOriginalPricing;
        }
        // Handle Adders
        $adderNames  = $request->input('adders_names', []);
        $adderPrices = $request->input('adders_prices', []);
        $adders_types = $request->input('adders_types', []);
        $adders_min_qty = $request->input('adders_min_qty', []);
        $adders_max_qty = $request->input('adders_max_qty', []);

        $adderIds = [];

        foreach ($adderNames as $i => $name) {
            $price = $adderPrices[$i] ?? 0;
            $type = $adders_types[$i] ?? 0;
            $min_qty = $adders_min_qty[$i] ?? 0;
            $max_qty = $adders_max_qty[$i] ?? 0;

            // Check if adder with same name exists globally
            $adder = Adder::firstOrCreate(['name' => $name]);

            // If price differs, update it
            if ($adder->price != $price) {
                $adder->price = $price;
            }
            $adder->type = $type;
            $adder->min_qty = $min_qty;
            $adder->max_qty = $max_qty;
            $adder->save();
            $adderIds[] = $adder->id;
        }

        // Attach new adders (sync replaces existing links cleanly)
        $category->adders()->sync($adderIds);

        // Save updated configuration & pricing
        $category->update([
            'configuration' => json_encode(['fields' => $updatedFields]),
            'pricing'       => json_encode($newPricing)
        ]);

        return response()->json(['message' => 'Category updated successfully']);
    }
}
