<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Bank;
use App\Models\User;
use Redirect;
use Session;

class BankController extends Controller
{
    /**
     * @return RedirectResponse|View
     */
    public function add(Request $request)
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

        $bankdata = self::doVerifyIban($request->input('iban'), $request->input('bic'));
        if ($bankdata->status == false) {
            Session::flash('flash_message', $bankdata->message);

            return Redirect::back();
        }

        $bank = Bank::create([
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

        return Redirect::route('user::dashboard');
    }

    /** @return RedirectResponse|View */
    public function edit()
    {
        $user = Auth::user();

        if ($user->bank == null) {
            return Redirect::route('user::bank::add');
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
            return Redirect::route('user::bank::add');
        }

        $bankdata = self::doVerifyIban($request->input('iban'), $request->input('bic'));
        if ($bankdata->status == false) {
            Session::flash('flash_message', $bankdata->message);

            return Redirect::back();
        }

        $bank = Bank::create([
            'iban' => $bankdata->iban,
            'bic' => $bankdata->bic,
            'machtigingid' => self::generateAuthorizationId($user),
        ]);

        $user->bank()->delete();
        $bank->user()->associate($user);
        $bank->save();

        Session::flash('flash_message', 'New withdrawal authorization added.');

        return Redirect::route('user::dashboard');
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

            return Redirect::route('user::dashboard');
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

        return Redirect::route('user::dashboard');
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
            $response->message = 'Your bank is not a member of SEPA (Single Euro Payments Area) so you can\'t use this bank account here. Please try another one.';

            return $response;
        }

        try {
            $country = substr($iban, 0, 2);

            if ($country == 'NL') {
                $response->bic = self::getNlBicFromIban($iban);
            }

            if ($response->bic != '' && self::verifyBic($response->bic)) {
                $response->status = false;
                $response->message = 'Your BIC is not valid.';

                return $response;
            }
        } catch (Exception $e) {
            if ($response->bic != '' && self::verifyBic($response->bic)) {
                $response->status = false;
                $response->message = 'Something went wrong retrieving your BIC.';

                return $response;
            }
        }

        return $response;
    }

    /**
     * @param  string  $bic
     * @return bool
     */
    public static function verifyBic($bic)
    {
        return preg_match('/([a-zA-Z]{4}[a-zA-Z]{2}[a-zA-Z0-9]{2}([a-zA-Z0-9]{3})?)/', $bic) !== 1;
    }

    /**
     * @param  User  $user
     * @return string
     */
    public static function generateAuthorizationId($user)
    {
        return 'PROTOX'.str_pad(strval($user->id), 5, '0', STR_PAD_LEFT).'X'.str_pad(strval(mt_rand(0, 99999)), 5, '0');
    }

    /**
     * @param  string  $iban
     * @return string|null
     */
    private static function getNlBicFromIban($iban)
    {
        $data = [ // Data from: https://www.betaalvereniging.nl/wp-content/uploads/BIC-lijst-NL.xlsx
            'AABN' => 'AABNNL2A',
            'ABNA' => 'ABNANL2A',
            'FTSB' => 'ABNANL2A',
            'ABNC' => 'ABNCNL2A',
            'ADYB' => 'ADYBNL2A',
            'AEGO' => 'AEGONL2U',
            'ANDL' => 'ANDLNL2A',
            'ARBN' => 'ARBNNL22',
            'ARSN' => 'ARSNNL21',
            'ASNB' => 'ASNBNL21',
            'ATBA' => 'ATBANL2A',
            'BARC' => 'BARCNL22',
            'BCDM' => 'BCDMNL22',
            'BCIT' => 'BCITNL2A',
            'BICK' => 'BICKNL2A',
            'BINK' => 'BINKNL21',
            'BITS' => 'BITSNL2A',
            'BKCH' => 'BKCHNL2R',
            'BKMG' => 'BKMGNL2A',
            'BLGW' => 'BLGWNL21',
            'BMEU' => 'BMEUNL21',
            'BNDA' => 'BNDANL2A',
            'BNGH' => 'BNGHNL2G',
            'BNPA' => 'BNPANL2A',
            'BOFA' => 'BOFANLNX',
            'BOFS' => 'BOFSNL21002',
            'BOTK' => 'BOTKNL2X',
            'BUNQ' => 'BUNQNL2A',
            'CHAS' => 'CHASNL2X',
            'CITC' => 'CITCNL2A',
            'CITI' => 'CITINL2X',
            'COBA' => 'COBANL2X',
            'DELE' => 'DELENL22',
            'DEUT' => 'DEUTNL2A',
            'DHBN' => 'DHBNNL2R',
            'DLBK' => 'DLBKNL2A',
            'DNIB' => 'DNIBNL2G',
            'EBUR' => 'EBURNL21',
            'EBPB' => 'EBPBNL22',
            'FBHL' => 'FBHLNL2A',
            'FLOR' => 'FLORNL2A',
            'FRNX' => 'FRNXNL2A',
            'FVLB' => 'FVLBNL22',
            'GILL' => 'GILLNL2A',
            'HAND' => 'HANDNL2A',
            'HHBA' => 'HHBANL22',
            'HSBC' => 'HSBCNL2A',
            'ICBC' => 'ICBCNL2A',
            'ICBK' => 'ICBKNL2A',
            'INGB' => 'INGBNL2A',
            'ISAE' => 'ISAENL2A',
            'ISBK' => 'ISBKNL2A',
            'KABA' => 'KABANL2A',
            'KASA' => 'KASANL2A',
            'KNAB' => 'KNABNL2H',
            'KOEX' => 'KOEXNL2A',
            'KRED' => 'KREDNL2X',
            'LOCY' => 'LOCYNL2A',
            'LOYD' => 'LOYDNL2A',
            'LPLN' => 'LPLNNL2F',
            'MHCB' => 'MHCBNL2A',
            'MOYO' => 'MOYONL21',
            'NNBA' => 'NNBANL2G',
            'NWAB' => 'NWABNL2G',
            'PCBC' => 'PCBCNL2A',
            'RABO' => 'RABONL2U',
            'RBRB' => 'RBRBNL21',
            'SNSB' => 'SNSBNL2A',
            'SOGE' => 'SOGENL2A',
            'TRIO' => 'TRIONL2U',
            'UGBI' => 'UGBINL2A',
            'VOWA' => 'VOWANL21',
            'VPAY' => 'VPAYNL22',
            'ZWLB' => 'ZWLBNL21',
        ];

        $bank = substr($iban, 4, 4);

        return array_key_exists($bank, $data) ? $data[$bank] : null;
    }
}
