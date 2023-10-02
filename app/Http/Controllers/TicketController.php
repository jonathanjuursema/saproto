<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PDF;
use App\Models\Event;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use Redirect;
use Session;

class TicketController extends Controller
{
    /** @return View */
    public function index()
    {
        return view('tickets.index', ['tickets' => Ticket::orderBy('id', 'desc')->paginate(20)]);
    }

    /** @return View */
    public function create()
    {
        return view('tickets.edit', ['ticket' => null]);
    }

    /**
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        if (! $request->has('is_members_only') && ! $request->has('is_prepaid') && ! Auth::user()->can('sysadmin')) {
            Session::flash('flash_message', 'Making tickets for external people payable via withdrawal is risky and usually not necessary. If you REALLY want this, please contact the Have You Tried Turning It Off And On Again committee.');

            return Redirect::back();
        }

        $ticket = new Ticket();
        $ticket->event_id = Event::findOrFail($request->input('event'))->id;
        $ticket->product_id = Product::findOrFail($request->input('product'))->id;
        $ticket->members_only = $request->has('is_members_only');
        $ticket->is_prepaid = $request->has('is_prepaid');
        $ticket->available_from = strtotime($request->input('available_from'));
        $ticket->available_to = strtotime($request->input('available_to'));
        $ticket->show_participants = $request->has('show_participants');
        $ticket->save();

        Session::flash('flash_message', 'The ticket has been created!');

        return Redirect::route('tickets::list');
    }

    /**
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);

        return view('tickets.edit', ['ticket' => $ticket]);
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if (! $request->has('is_members_only') && ! $request->has('is_prepaid') && ! Auth::user()->can('sysadmin')) {
            Session::flash('flash_message', 'Making tickets for external people payable via withdrawal is risky and usually not necessary. If you REALLY want this, please contact the Have You Tried Turninig It Off And On Again committee.');

            return Redirect::back();
        }

        /** @var Ticket $ticket */
        $ticket = Ticket::findOrFail($id);

        if ($request->has('event')) {
            $ticket->event_id = Event::findOrFail($request->input('event'))->id;
        }
        if ($request->has('product')) {
            $ticket->product_id = Product::findOrFail($request->input('product'))->id;
        }

        $ticket->members_only = $request->has('is_members_only');
        $ticket->is_prepaid = $request->has('is_prepaid');
        $ticket->available_from = strtotime($request->input('available_from'));
        $ticket->available_to = strtotime($request->input('available_to'));
        $ticket->show_participants = $request->has('show_participants');
        $ticket->save();

        Session::flash('flash_message', 'The ticket has been updated!');

        return Redirect::route('tickets::list');
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::findOrFail($id);
        if ($ticket->purchases()->count() > 0) {
            Session::flash('flash_message', 'This ticket has already been sold, you cannot remove it!');

            return Redirect::back();
        }
        $ticket->delete();

        Session::flash('flash_message', 'The ticket has been deleted!');

        return Redirect::route('tickets::list');
    }

    /**
     * @param  string  $barcode
     * @return RedirectResponse
     */
    public function scan($barcode)
    {
        $ticket = TicketPurchase::where('barcode', $barcode)->first();
        if ($ticket && ! $ticket->ticket->event->isEventAdmin(Auth::user())) {
            Session::flash('flash_message', 'You are not allowed to scan for this event.');

            return Redirect::back();
        }
        if ($ticket) {
            $ticket->scanned = date('Y-m-d H:i:s');
            $ticket->save();
            Session::flash('flash_message', 'Ticket has been scanned!');
        } else {
            Session::flash('flash_message', 'Unknown ticket.');
        }

        return Redirect::back();
    }

    /**
     * @param  int  $event
     * @return array
     */
    public function scanApi($event, Request $request)
    {
        if (! $request->has('barcode')) {
            return [
                'code' => 500,
                'message' => 'Missing barcode',
                'data' => null,
            ];
        }

        $unscan = $request->has('unscan');

        $event = Event::find($event);
        if ($event === null) {
            return [
                'code' => 500,
                'message' => 'Unknown event',
                'data' => null,
            ];
        }

        /** @var TicketPurchase|null $ticket */
        $ticket = TicketPurchase::where('barcode', $request->barcode)->first();

        if ($ticket != null && ! $ticket->ticket->event->isEventAdmin(Auth::user())) {
            return [
                'code' => 500,
                'message' => 'Unauthorized to scan',
                'data' => null,
            ];
        }

        if ($ticket != null) {
            $ticket->load('user', 'orderline', 'ticket', 'ticket.product');
            if ($ticket->ticket->event_id != $event->id) {
                return [
                    'code' => 500,
                    'message' => 'Ticket not for this event',
                    'data' => null,
                ];
            }
            if (! $unscan && $ticket->scanned !== null) {
                return [
                    'code' => 403,
                    'message' => 'Ticket already used',
                    'data' => $ticket,
                ];
            }
            if ($unscan && $ticket->scanned == null) {
                return [
                    'code' => 403,
                    'message' => 'Ticket has not been used yet',
                    'data' => $ticket,
                ];
            }
            if ($ticket->canBeDownloaded() === false) {
                return [
                    'code' => 500,
                    'message' => 'Ticket Not Paid For',
                    'data' => $ticket,
                ];
            }
            $ticket->scanned = date('Y-m-d H:i:s');
            if ($unscan) {
                $ticket->scanned = null;
            }
            $ticket->save();

            return [
                'code' => 200,
                'message' => 'Valid ticket',
                'data' => $ticket,
            ];
        } else {
            return [
                'code' => 500,
                'message' => 'Unknown barcode',
                'data' => null,
            ];
        }
    }

    /**
     * @param  string  $barcode
     * @return RedirectResponse
     */
    public function unscan($barcode = null)
    {
        if ($barcode == null) {
            Session::flash('flash_message', 'No valid barcode presented!');

            return Redirect::back();
        }

        $ticket = TicketPurchase::where('barcode', $barcode)->first();
        if ($ticket && ! $ticket->ticket->event->isEventAdmin(Auth::user())) {
            Session::flash('flash_message', 'You are not allowed to scan for this event.');

            return Redirect::back();
        }

        if ($ticket) {
            $ticket->scanned = null;
            $ticket->save();
            Session::flash('flash_message', 'Ticket has been unscanned!');
        } else {
            Session::flash('flash_message', 'Unknown ticket.');
        }

        return Redirect::back();
    }

    /**
     * @param  int  $id
     * @return string
     */
    public function download($id)
    {
        /** @var TicketPurchase $ticket */
        $ticket = TicketPurchase::findOrFail($id);
        if ($ticket->user->id != Auth::id()) {
            abort(403, 'This is not your ticket!');
        } elseif (! $ticket->canBeDownloaded()) {
            Session::flash('flash_message', 'You need to pay for this ticket before you can download it.');

            return Redirect::back();
        }

        $pdf = new PDF('P', 'A4', 'en');
        $pdf->writeHTML(view('tickets.download', ['ticket' => $ticket]));

        return $pdf->output(sprintf('saproto-ticket-%s.pdf', $ticket->id));
    }

    /**
     * @param  int  $id
     * @return RedirectResponse
     */
    public function buyForEvent(Request $request, $id)
    {
        /** @var Event $event */
        $event = Event::findOrFail($id);

        if ($event->tickets->count() < 1) {
            Session::flash('flash_message', 'There are no tickets available for this event.');

            return Redirect::back();
        }

        if (! $request->has('tickets')) {
            Session::flash('flash_message', 'There are no tickets available for this event.');

            return Redirect::back();
        }

        foreach ($request->get('tickets') as $ticket_id => $amount) {
            $ticket = Ticket::find($ticket_id);
            if (! $ticket) {
                Session::flash('flash_message', "Ticket ID#$ticket_id is not an existing ticket. Entire order cancelled.");

                return Redirect::back();
            }
            if ($ticket->members_only && ! Auth::user()->is_member) {
                Session::flash('flash_message', "Ticket ID#$ticket_id is only available to members. You are not a member. Entire order cancelled.");

                return Redirect::back();
            }
            if ($ticket->event->id != $event->id) {
                Session::flash('flash_message', "Ticket ID#$ticket_id is not a ticket for event '".$event->title."'. Entire order cancelled.");

                return Redirect::back();
            }
            if ($amount > config('proto.maxtickets')) {
                Session::flash('flash_message', 'You tried to more then '.config('proto.maxtickets')." of ticket '".$ticket->product->name."', you can only buy ".config('proto.maxtickets').' at a time. Entire order cancelled.');

                return Redirect::back();
            }
            if ($amount > $ticket->product->stock) {
                Session::flash('flash_message', "You tried to buy $amount of ticket '".$ticket->product->name."', but only ".$ticket->product->stock.' are available. Entire order cancelled.');

                return Redirect::back();
            }
        }

        $sold = false;

        $prepaid_tickets = [];

        $total_cost = 0;
        foreach ($request->get('tickets') as $ticket_id => $amount) {
            $ticket = Ticket::find($ticket_id);

            for ($i = 0; $i < $amount; $i++) {
                $oid = $ticket->product->buyForUser(Auth::user(), 1, $ticket->product->price, null, null, null, sprintf('ticket_bought_by_%u', Auth::user()->id));

                if ($ticket->is_prepaid) {
                    $prepaid_tickets[] = $oid;
                    $total_cost += $ticket->product->price;
                }

                $purchase = TicketPurchase::create([
                    'ticket_id' => $ticket_id,
                    'orderline_id' => $oid,
                    'user_id' => Auth::id(),
                    'barcode' => $oid.mt_rand(1000000000, 9999999999),
                ]);
                $purchase->save();

                $sold = true;
            }
        }

        $payment_method = '';
        if (config('omnomcom.mollie.use_fees') && ! $request->has('method') && count($prepaid_tickets) > 0) {
            Session::flash('flash_message', 'No payment method is selected!');

            return Redirect::back();
        }

        // check if total ticket cost is allowed at this payment_method and validate the selected method
        if (config('omnomcom.mollie.use_fees') && count($prepaid_tickets) != 0) {
            $available_methods = MollieController::getPaymentMethods();
            $requested_method = $request->get('method');
            $payment_method = $available_methods->filter(function ($method) use ($requested_method) {
                return $method->id === $requested_method;
            });

            if ($payment_method->count() === 0) {
                Session::flash('flash_message', 'The selected payment method is unavailable, please select a different method');

                return Redirect::back();
            }
            $payment_method = $payment_method->first();

            if (
                $total_cost < floatval($payment_method->minimumAmount->value) ||
                $total_cost > floatval($payment_method->maximumAmount->value)
            ) {
                Session::flash('flash_message', 'You are unable to pay this amount with the selected method!');

                return Redirect::back();
            }
        }

        if (! $sold) {
            Session::flash('flash_message', "You didn't select any tickets to buy. Maybe buy some tickets?");

            return Redirect::back();
        }

        if (count($prepaid_tickets) > 0) {
            Session::put('prepaid_tickets', $event->id);
            $transaction = MollieController::createPaymentForOrderlines($prepaid_tickets, $payment_method);

            OrderLine::whereIn('id', $prepaid_tickets)->update(['payed_with_mollie' => $transaction->id]);

            return Redirect::to($transaction->payment_url);
        }

        Session::flash('flash_message', 'Order completed succesfully! You can find your tickets on this event page.');

        if ($ticket->redirect_url) {
            return Redirect::away($ticket->redirect_url);
        }

        return Redirect::back();
    }
}
