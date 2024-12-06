<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StockMutationController extends Controller
{
    /**
     *  Filters mutations using the filter form
     */
    public function filterMutations(Request $rq, ?array $selection = null)
    {
        $mutations = StockMutation::query()->orderBy('stock_mutations.created_at', 'desc')->orderBy('stock_mutations.id', 'desc');

        // Find mutations by Pwoduct
        if ($rq->has('product_name') && strlen($rq->get('product_name')) > 2) {
            $search = $rq->get('product_name');
            $mutations = $mutations
                ->join('products', 'products.id', '=', 'stock_mutations.product_id', 'inner')
                ->where('products.name', 'like', "%{$search}%");
        }

        // Find mutations by authoring User
        if ($rq->has('author_name') && strlen($rq->input('author_name')) > 2) {
            $search = $rq->get('author_name');
            $mutations = $mutations
                ->join('users', 'users.id', '=', 'stock_mutations.user_id', 'inner')
                ->where('users.name', 'like', "%{$search}%");
        }

        // Find mutations before given date
        if ($rq->has('before')) {
            $before = Carbon::parse($rq->input('before'));
            $mutations = $mutations->where('stock_mutations.created_at', '<=', $before);
        }

        // Find mutations made after given date
        if ($rq->has('after')) {
            $after = Carbon::parse($rq->input('after'));
            $mutations = $mutations->where('stock_mutations.created_at', '>', $after);
        }

        // Filter mwutations by them being a loss
        if (! $rq->has('also_positive')) {
            $mutations = $mutations->whereColumn('stock_mutations.before', '>', 'stock_mutations.after');
        }

        // variables for SELECT statement
        // We need this to filter out the data from joins
        // Also prevents shadowing/disappearance of the created_at field
        if ($selection == null) {
            $selection = ['stock_mutations.product_id', 'stock_mutations.user_id', 'stock_mutations.created_at', 'stock_mutations.before', 'stock_mutations.after', 'stock_mutations.is_bulk'];
        }

        return $mutations->select($selection);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $rq)
    {
        return view('omnomcom.products.mutations', ['mutations' => $this->filterMutations($rq)->paginate(15)]);
    }

    public function generateCsv(Request $rq)
    {
        // Exclude the userid, which we don't need for reports
        $selection = ['stock_mutations.product_id', 'stock_mutations.before', 'stock_mutations.after', 'stock_mutations.created_at'];
        $mutations = $this->filterMutations($rq, $selection)->get()->toArray();

        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=mutations.csv',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = static function () use ($mutations) {
            $f = fopen('php://output', 'w');
            $csv_header = ['Product ID', 'Product Name', 'Change', 'Old stock', 'Updated stock', 'Creation time'];
            fputcsv($f, $csv_header);
            foreach ($mutations as $row) {
                $product = Product::query()->find($row['product_id']);

                if (! is_null($product)) {
                    fputcsv($f, [$row['product_id'], $product->name, $row['after'] - $row['before'], $row['before'], $row['after'], $row['created_at']]);
                }
            }

            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}
