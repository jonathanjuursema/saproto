<?php

namespace App\Http\Controllers;

use App\Models\QrAuthRequest;
use App\Models\RfidCard;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class RfidCardController extends Controller
{
    /**
     * @return array This method returns raw HTML and is intended to be used via AJAX!
     *
     * @throws Exception
     */
    public function store(Request $request): array
    {
        switch ($request->input('credentialtype')) {
            case 'qr':
                $qrAuthRequest = QrAuthRequest::query()->where('auth_token', $request->input('credentials'))->first();
                if (! $qrAuthRequest) {
                    return ['ok' => false, 'text' => 'Invalid authentication token.'];
                }

                $user = $qrAuthRequest->authUser();
                if (! $user) {
                    return ['ok' => false, 'text' => "QR authentication hasn't been completed."];
                }

                break;

            default:
                return ['ok' => false, 'text' => 'Invalid credential type.'];
        }

        if (! $user->is_member) {
            return ['ok' => false, 'text' => 'You must be a member to use the OmNomCom.'];
        }

        $uid = $request->input('card');
        if (strlen($uid) == 0) {
            return ['ok' => false, 'text' => 'Empty card UID provided. Did you scan your card properly?'];
        }

        $card = RfidCard::query()->where('card_id', $uid)->first();
        if ($card) {
            if ($card->user->id == $user->id) {
                return ['ok' => false, 'text' => 'This card is already registered to you!'];
            }

            return ['ok' => false, 'text' => 'This card is already registered to someone.'];
        }

        $card = RfidCard::query()->create([
            'user_id' => $user->id,
            'card_id' => $uid,
        ]);
        $card->save();

        return ['ok' => true, 'text' => 'This card has been successfully registered to '.$user->name];
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        /** @var RfidCard $rfid */
        $rfid = RfidCard::query()->findOrFail($id);
        if (($rfid->user->id != Auth::id()) && (! Auth::user()->can('board'))) {
            abort(403);
        }

        return view('users.rfid.edit', ['card' => $rfid]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /** @var RfidCard $rfid */
        $rfid = RfidCard::query()->findOrFail($id);
        if ($rfid->user->id != Auth::id()) {
            abort(403);
        }

        $rfid->name = $request->input('name');
        $rfid->save();

        Session::flash('flash_message', 'Your RFID card has been updated.');

        return Redirect::route('user::dashboard::show');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, $id)
    {
        /** @var RfidCard $rfid */
        $rfid = RfidCard::query()->findOrFail($id);
        if ($rfid->user->id != Auth::id()) {
            abort(403);
        }

        $rfid->delete();

        Session::flash('flash_message', 'Your RFID card has been deleted.');

        return Redirect::back();
    }
}
