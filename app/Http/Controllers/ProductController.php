<?php

namespace Proto\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mail;
use Proto\Mail\ProductBulkUpdateNotification;
use Proto\Models\Account;
use Proto\Models\Product;
use Proto\Models\ProductCategory;
use Proto\Models\StockMutation;
use Proto\Models\StorageEntry;
use Redirect;
use Session;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * @return View
     */
    public function index(Request $request)
    {
        if ($request->has('search') && strlen($request->get('search')) > 2) {
            $search = $request->get('search');
            $products = Product::where('name', 'like', "%$search%")->orderBy('is_visible', 'desc')->orderBy('name', 'asc')->limit(100)->get();
        } elseif ($request->has('filter')) {
            switch ($request->get('filter')) {
                case 'invisible':
                    $products = Product::where('is_visible', false)->orderBy('name', 'asc')->get();
                    break;
                default:
                    $products = Product::orderBy('is_visible', 'desc')->orderBy('name', 'asc')->paginate(20);
                    break;
            }
        } else {
            $products = Product::orderBy('is_visible', 'desc')->orderBy('name', 'asc')->paginate(20);
        }

        return view('omnomcom.products.index', ['products' => $products]);
    }

    /**
     * @return View
     */
    public function create()
    {
        return view('omnomcom.products.edit', [
            'product' => null,
            'accounts' => Account::orderBy('account_number', 'asc')->get(),
            'categories' => ProductCategory::all(),
        ]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function store(Request $request)
    {
        $product = Product::create($request->except('image', 'product_categories'));
        $product->price = floatval(str_replace(',', '.', $request->price));
        $product->is_visible = $request->has('is_visible');
        $product->is_alcoholic = $request->has('is_alcoholic');
        $product->is_visible_when_no_stock = $request->has('is_visible_when_no_stock');

        if ($request->file('image')) {
            $file = new StorageEntry();
            $file->createFromFile($request->file('image'));

            $product->image()->associate($file);
        }

        $categories = [];
        if ($request->has('product_categories') && count($request->input('product_categories')) > 0) {
            foreach ($request->input('product_categories') as $category) {
                $category = ProductCategory::find($category);
                if ($category != null) {
                    $categories[] = $category->id;
                }
            }
        }
        $product->categories()->sync($categories);

        $product->save();

        Session::flash('flash_message', 'The new product has been created!');

        return Redirect::route('omnomcom::products::list', ['search' => $product->name]);
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('omnomcom.products.edit', [
            'product' => $product,
            'accounts' => Account::orderBy('account_number', 'asc')->get(),
            'categories' => ProductCategory::all(),
            'orderlines' => $product->orderlines()->orderBy('created_at', 'DESC')->paginate(20),
        ]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws FileNotFoundException
     */
    public function update(Request $request, $id)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        // Mutation logging point
        $old_stock = $product->stock;
        $found_stock = $request->input('prev_stock');
        $new_stock = $request->input('stock');

        // Splitting mutation into two since restock and loss marking happen with one edit
        if ($old_stock != $found_stock) {
            // Stock observation mutation
            // Is this how you make them records? Is there a better way?
            $pre_mut = StockMutation::make([
                'before' => $old_stock,
                'after' => $found_stock,
                'is_bulk' => false]
            );

            $pre_mut->user()->associate($request->user());
            $pre_mut->product()->associate($product);
            $pre_mut->save();
        }

        if ($found_stock != $new_stock) {
            // Actwual restocking mutation
            $after_mut = StockMutation::make([
                'before' => $found_stock,
                'after' => $new_stock,
                'is_bulk' => false]
            );

            $after_mut->user()->associate($request->user());
            $after_mut->product()->associate($product);
            $after_mut->save();
        }

        $product->fill($request->except('image', 'product_categories'));
        $product->price = floatval(str_replace(',', '.', $request->price));
        $product->is_visible = $request->has('is_visible');
        $product->is_alcoholic = $request->has('is_alcoholic');
        $product->is_visible_when_no_stock = $request->has('is_visible_when_no_stock');

        if ($request->file('image')) {
            $file = new StorageEntry();
            $file->createFromFile($request->file('image'));

            $product->image()->associate($file);
        }

        $product->account()->associate(Account::findOrFail($request->input('account_id')));

        $categories = [];
        if ($request->has('product_categories') && count($request->input('product_categories')) > 0) {
            foreach ($request->input('product_categories') as $category) {
                $category = ProductCategory::find($category);
                if ($category != null) {
                    $categories[] = $category->id;
                }
            }
        }
        $product->categories()->sync($categories);

        $product->save();

        Session::flash('flash_message', 'The product has been updated.');

        return Redirect::route('omnomcom::products::edit', ['id' => $product->id]);
    }

    /**
     * @return RedirectResponse
     */
    public function bulkUpdate(Request $request)
    {
        $input = preg_split('/\r\n|\r|\n/', $request->input('update'));

        $log = '';
        $errors = '';

        $products = [];
        $deltas = [];

        foreach ($input as $lineRaw) {
            $line = explode(',', $lineRaw);

            if (count($line) == 2) {
                $product = Product::find($line[0]);

                if ($product) {
                    $delta = intval($line[1]);

                    $old_stock = $product->stock;
                    $new_stock = $old_stock + $delta;

                    $log .= '<strong>'.$product->name.'</strong> updated with delta <strong>'.$line[1]."</strong>. Stock changed from $old_stock to <strong>$new_stock</strong>.<br>";

                    $products[] = $product->id;
                    $deltas[] = $delta;
                } else {
                    $errors .= "<span style='color: red;'>Product ID <strong>".$line[0].'</strong> not recognized.</span><br>';
                }
            } else {
                $errors .= "<span style='color: red;'>Incorrect format for line <strong>".$lineRaw.'</strong>.</span><br>';
            }
        }

        foreach ($products as $i => $product_id) {
            $product = Product::find($product_id);

            // Make product mutations for bulk updates
            $mutation = StockMutation::make([
                'before' => $product->stock,
                'after' => $product->stock + $deltas[$i],
                'is_bulk' => true]);

            $mutation->user()->associate($request->user());
            $mutation->product()->associate($product);
            $mutation->save();

            $product->stock += $deltas[$i];
            $product->save();
        }

        Session::flash('flash_message', 'Done. Errors:<br>'.$errors);
        Mail::queue((new ProductBulkUpdateNotification(Auth::user(), $errors.$log))->onQueue('low'));

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        if ($id == config('omnomcom.dinnerform-product') || $id == config('omnomcom.failed-withdrawal')) {
            Session::flash('flash_message', 'You cannot delete this product because it is used in the source code of the website');

            return Redirect::back();
        }
        /** @var Product $product */
        $product = Product::findOrFail($id);

        if ($product->orderlines->count() > 0) {
            Session::flash('flash_message', 'You cannot delete this product because there are orderlines associated with it.');

            return Redirect::back();
        }

        if ($product->ticket) {
            Session::flash('flash_message', 'You cannot delete this product because there is still a ticket associated with it.');

            return Redirect::back();
        }

        $product->delete();

        Session::flash('flash_message', 'The product has been deleted.');

        return Redirect::route('omnomcom::products::list');
    }

    /** @return StreamedResponse A CSV file with all product info. */
    public function generateCsv()
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=products.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $data = Product::all()->toArray();
        array_unshift($data, array_keys($data[0]));

        $callback = function () use ($data) {
            $f = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($f, $row);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}
