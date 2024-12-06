<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use PDF;
use Spipu\Html2Pdf\Exception\Html2PdfException;

class MemberCardController extends Controller
{
    /**
     * @param  int  $id
     * @return string
     *
     * @throws Html2PdfException
     */
    public function download(Request $request, $id)
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);

        if (! $user->is_member) {
            abort(403, 'Only members can have a member card printed.');
        }

        $card = new PDF('L', [86, 54], 'en');
        $card->writeHTML(view('users.membercard.membercard', ['user' => $user, 'overlayonly' => $request->has('overlayonly')]));

        return $card->output();
    }

    public function startPrint(Request $request): string
    {
        $user = User::query()->find($request->input('id'));

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
