<?php

namespace App\Http\Controllers;

use App\Models\items as ModelsItems;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class items extends Controller
{
    public function all_item(Request $request) {
        //mengubah enum yang berbentuk string,semisal kapital menjadi lowercasse
        if ($request->has('status')) {
            $request->merge(['status' => strtolower($request->query('status'))]);
        }
        if ($request->has('order')) {
            $request->merge(['order' => strtolower($request->query('order'))]);
        }
        $validate = Validator::make($request->all(),[
            'search' => 'nullable|string',
            'category' => 'nullable|string',
            'status' => 'nullable|string',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer|max:50',
            'sort' => 'nullable|string',
            'order' => 'nullable|string',
        ]);

        $validated = $validate->validate(); //berfungsi jika ada yang error otomatis kirim response 422
        $search = $validated['search'] ?? '';
        $status = $validated['status'] ?? '';
        $category = $validated['category'] ?? '';
        $page = $validated['page'] ?? 1;
        $per_page = $validated['per_page'] ?? 10;
        $sort = $validated['sort'] ?? 'created_at';
        $order = $validated['order'] ?? 'desc';

        $data = ModelsItems::query()
            ->when(filled($search), function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%");
            })
            ->when(filled($status), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when(filled($category) && ModelsItems::where('slug', $category)->exists(), function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->orderBy($sort, $order)
            ->paginate($per_page);

        return response()->json($data);
    }

    public function by_item(Request $request,$id) {
        $data = ModelsItems::where('id', $id)->first();

        if (!$data) {
            return response()->json([
                'message' => 'nothing in here to scrap, BOT!!'
            ],404);
        }
        // dd($data);
        return response()->json([
            'data' => $data
        ],200);
    }

    public function up_item(Request $request) {
        $valid = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:categories,id',
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:20',
            'address' => 'string|max:255|nullable',
            'city' => 'string|max:100|nullable',
            'phone' => 'string|max:30|nullable',
            'image' => 'string|max:2048|nullable',
            'status' => 'in:draft,published|nullable'
        ]);

        if ($valid->fails()) {
            return response($valid->errors(),422);
        }

        $validated = $valid->validate();
        $cleaned = array_filter($validated, fn ($value) => $value != null);
        $cleaned['user_id'] = $request->user()->id;
        $cleaned['slug'] = Str::slug(strtolower($request->title));
        if (ModelsItems::where('slug', $cleaned['slug'])->exists()) {
            return response()->json('data duplikat',422);
        }
        $make = ModelsItems::create($cleaned);
        $make['owner'] = [
            'id' => $request->user()->id,
            'name' => $request->user()->name
        ];
        return response()->json([
            'data' => $make
        ],201);
    }

    public function put_item(Request $request,$id) {
        $item = ModelsItems::where('id', $id);
        if (!$item->exists()) {
            return response()->json([
                'message' => 'data tidak ditemukan'
            ],404);
        }

        $item->update([
            'category_id' => $request->input('category_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'status' => $request->input('status'),
            'slug' => $request->filled('title') ? Str::slug(strtolower($request->title)) : $item->slug,
            'phone' => $request->input('phone'),
            'image' => $request->input('image'),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'data' => $item->get()
        ]);
    }

    public function patch_item(Request $request,$id) {
        $item = ModelsItems::find($id);

        if (!$item->exists()) {
            return response()->json([
                'message' => 'data tidak ditemukan'
            ],404);
        }
        // dd($item->get('slug'));
        $itemonly = $request->only(['category_id', 'title', 'name', 'description', 'address', 'city', 'status', 'phone', 'image']);
        $itemonly['updated_at'] = Carbon::now();
        $slug = $request->filled('title') ? Str::slug(strtolower($request->title)) : $item->slug;
        $itemonly['slug'] = $slug;
        $item->update($itemonly);
        return response()->json([
            'data' => $item->get()
        ]);
    }

    public function del_item(Request $request,$id) {
        $item = ModelsItems::find($id);
        if (!$item) {
            return response()->json([
                'message' => 'item tidak ditemukan'
            ],404);
        }
        $item->delete();
        return response()->json([
            'message' => 'Data berhasil dihapus'
        ],200);
    }
}
