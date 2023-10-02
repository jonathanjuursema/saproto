<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\User;

class MemberCardController extends Controller
{
    /**
     * @param  int  $id
     * @return string
     */
    public function download(Request $request, $id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

        if (! $user->is_member) {
            abort(403, 'Only members can have a member card printed.');
        }

        $card = new PDF('L', [86, 54], 'en');
        $card->writeHTML(view('users.membercard.membercard', ['user' => $user, 'overlayonly' => $request->has('overlayonly')]));

        return $card->output();
    }

    /**
     * @return string
     */
    public function startPrint(Request $request)
    {
        $user = User::find($request->input('id'));

        if (! $user) {
            return 'This user could not be found!';
        }

        if (! $user->is_member) {
            return 'Only members can have their card printed!';
        }

        $result = FileController::requestPrint('card', route('membercard::download', ['id' => $user->id]));
        $user->member->card_printed_on = date('Y-m-d');
        $user->member->save();

        return 'The printer service responded: '.$result;
    }
}
