<?php

namespace App\Http\Controllers;

use AbcAeffchen\SepaUtilities\SepaUtilities;
use App\Models\Bank;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class BankController extends Controller
{
    /**
     * @return RedirectResponse|View
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        if ($user->bank != null) {
            return Redirect::route('user::bank::edit');
        }

        if ($request->wizard) {
            Session::flash('wizard', true);
        }

        return view('users.bankaccounts.addbank', ['user' => $user, 'new' => true]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (! SepaUtilities::checkIBAN($request->input('iban'))) {
            Session::flash('flash_message', 'Your IBAN is not valid.');

            return Redirect::back();
        }

        if (! SepaUtilities::checkBIC($request->input('bic'))) {
            Session::flash('flash_message', 'Your BIC is not valid.');

            return Redirect::back();
        }

        if (! SepaUtilities::crossCheckIbanBic($request->input('iban'), $request->input('bic'))) {
            Session::flash('flash_message', 'Your IBAN and BIC do not match.');

            return Redirect::back();
        }

        $bankdata = self::doVerifyIban($request->input('iban'), $request->input('bic'));
        if ($bankdata->status == false) {
            Session::flash('flash_message', $bankdata->message);

            return Redirect::back();
        }

        $bank = Bank::query()->create([
            'iban' => $bankdata->iban,
            'bic' => $bankdata->bic,
            'machtigingid' => self::generateAuthorizationId($user),
        ]);

        $user->bank()->delete();
        $bank->user()->associate($user);
        $bank->save();

        Session::flash('flash_message', 'New withdrawal authorization added.');

        if (Session::get('wizard')) {
            return Redirect::route('becomeamember');
        }

        return Redirect::route('user::dashboard::show');
    }

    /** @return RedirectResponse|View */
    public function edit()
    {
        $user = Auth::user();

        if ($user->bank == null) {
            return Redirect::route('user::bank::create');
        }

        return view('users.bankaccounts.addbank', ['user' => $user, 'new' => false]);
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->bank == null) {
            return Redirect::route('user::bank::create');
        }

        $bankdata = self::doVerifyIban($request->input('iban'), $request->input('bic'));
        if ($bankdata->status == false) {
            Session::flash('flash_message', $bankdata->message);

            return Redirect::back();
        }

        $bank = Bank::query()->create([
            'iban' => $bankdata->iban,
            'bic' => $bankdata->bic,
            'machtigingid' => self::generateAuthorizationId($user),
        ]);

        $user->bank()->delete();
        $bank->user()->associate($user);
        $bank->save();

        Session::flash('flash_message', 'New withdrawal authorization added.');

        return Redirect::route('user::dashboard::show');
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy()
    {
        $user = Auth::user();

        if ($user->bank == null) {
            Session::flash('flash_message', "You don't have a bank authorization to revoke.");

            return Redirect::route('user::dashboard::show');
        }

        if ($user->is_member) {
            Session::flash('flash_message', 'As a member you cannot revoke your bank authorization. You can update it, though.');

            return Redirect::back();
        }

        if ($user->hasUnpaidOrderlines()) {
            Session::flash('flash_message', 'You cannot revoke your bank authorization while you still have unpaid orderlines.');

            return Redirect::back();
        }

        $user->bank->delete();

        Session::flash('flash_message', 'Deleted bank account.');

        return Redirect::route('user::dashboard::show');
    }

    /**
     * @return false|string
     */
    public function verifyIban(Request $request)
    {
        return json_encode(self::doVerifyIban($request->input('iban'), $request->input('bic')));
    }

    /**
     * @param  string  $iban
     * @param  string|null  $bic
     * @return object
     */
    public static function doVerifyIban($iban, $bic = null)
    {
        $iban = strtoupper($iban);

        $response = (object) [
            'status' => true,
            'message' => 'Valid',
            'iban' => iban_to_machine_format($iban),
            'bic' => str_replace(' ', '', strtoupper($bic)),
        ];

        if (! verify_iban($response->iban)) {
            $response->status = false;
            $response->message = 'Your IBAN is not valid.';

            return $response;
        }

        if (! iban_country_is_sepa(iban_get_country_part($response->iban))) {
            $response->status = false;
            $response->message = "Your bank is not a member of SEPA (Single Euro Payments Area) so you can't use this bank account here. Please try another one.";

            return $response;
        }

        try {
            if (! self::verifyBicIsValid($response->bic)) {
                $response->status = false;
                $response->message = 'Your BIC is not valid.';

                return $response;
            }
        } catch (Exception) {
            if (! self::verifyBicIsValid($response->bic)) {
                $response->status = false;
                $response->message = 'Something went wrong retrieving your BIC.';

                return $response;
            }
        }

        return $response;
    }

    /**
     * @param  string  $bic
     */
    public static function verifyBicIsValid($bic): bool
    {
        if (($bic == '')) {
            return false;
        }

        return preg_match('/([a-zA-Z]{4}[a-zA-Z]{2}[a-zA-Z0-9]{2}([a-zA-Z0-9]{3})?)/', $bic) === 1;
    }

    /**
     * @param  User  $user
     */
    public static function generateAuthorizationId($user): string
    {
        return 'PROTOX'.str_pad(strval($user->id), 5, '0', STR_PAD_LEFT).'X'.str_pad(strval(mt_rand(0, 99999)), 5, '0');
    }
}
