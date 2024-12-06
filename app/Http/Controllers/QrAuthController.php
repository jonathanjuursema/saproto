<?php

namespace App\Http\Controllers;

use App\Models\QrAuthRequest;
use Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Milon\Barcode\DNS2D;

class QrAuthController extends Controller
{
    /**
     * @param  string  $code
     * @return Response
     */
    public function showCode($code)
    {
        $qrAuthRequest = QrAuthRequest::query()->where('qr_token', '=', $code)->first();

        if ($qrAuthRequest == null) {
            abort(404);
        }

        return response((new DNS2D)->getBarcodeSVG(route('qr::dialog', $qrAuthRequest->qr_token), 'QRCODE'))->header('Content-Type', 'image/svg+xml');
    }

    public function generateRequest(Request $request): QrAuthRequest
    {
        if (! $request->has('description')) {
            abort(500, 'No description was provided.');
        }

        $qrAuthRequest = new QrAuthRequest;
        $qrAuthRequest->description = $request->description;
        $qrAuthRequest->qr_token = Str::random(8);
        $qrAuthRequest->auth_token = md5(Str::random(40));
        $qrAuthRequest->save();

        return $qrAuthRequest;
    }

    /**
     * @param  string  $code
     * @return View
     */
    public function showDialog($code)
    {
        $qrAuthRequest = QrAuthRequest::query()->where('qr_token', '=', $code)->first();

        if (! $qrAuthRequest) {
            abort(404);
        }

        return view('auth.qr.dialog', ['description' => $qrAuthRequest->description, 'code' => $qrAuthRequest->qr_token]);
    }

    /**
     * @param  string  $code
     * @return View
     */
    public function approve($code)
    {
        $qrAuthRequest = QrAuthRequest::query()->where('qr_token', '=', $code)->first();

        if (! $qrAuthRequest) {
            abort(404);
        }

        $qrAuthRequest->approved_at = Carbon::now();
        $qrAuthRequest->user_id = Auth::id();

        $qrAuthRequest->save();

        return view('auth.qr.done');
    }

    /**
     * @param  string  $code
     * @return JsonResponse
     */
    public function apiApprove($code)
    {
        $qrAuthRequest = QrAuthRequest::query()->where('qr_token', '=', $code)->first();

        if (! $qrAuthRequest) {
            abort(403);
        }

        $qrAuthRequest->approved_at = Carbon::now();
        $qrAuthRequest->user_id = Auth::id();

        $qrAuthRequest->save();

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * @param  string  $code
     * @return JsonResponse
     */
    public function apiInfo($code)
    {
        $qrAuthRequest = QrAuthRequest::query()->where('qr_token', '=', $code)->first();

        if (! $qrAuthRequest) {
            abort(404);
        }

        return response()->json(['description' => $qrAuthRequest->description], 200);
    }

    public function isApproved(Request $request): string
    {
        $qrAuthRequest = QrAuthRequest::query()->where('auth_token', '=', $request->code)->first();

        if (! $qrAuthRequest) {
            abort(404);
        }

        if ($qrAuthRequest->isApproved()) {
            return 'true';
        }

        return 'false';
    }
}
