<?php

namespace App\Console\Commands;

use App\Enums\MembershipTypeEnum;
use App\Mail\FeeEmail;
use App\Mail\FeeEmailForBoard;
use App\Models\Member;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class FeeCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:feecron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cronjob that takes care of charging the membership fee.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): int
    {
        if (intval(date('n')) == 8 || intval(date('n')) == 9) {
            $this->info("We don't charge membership fees in August or September.");

            return 0;
        }

        $yearstart = intval(date('n')) >= 9 ? intval(date('Y')) : intval(date('Y')) - 1;

        $usersToCharge = User::query()->whereHas('member', function ($q) {
            $q->whereNot('membership_type', MembershipTypeEnum::PENDING);
        })->whereDoesntHave('orderlines', function ($q) use ($yearstart) {
            $q->whereIn('product_id', array_values(Config::array('omnomcom.fee')))->where('created_at', '>=', $yearstart.'-09-01 00:00:01');
        })->with('member.UtAccount');

        $charged = (object) [
            'count' => 0,
            'regular' => [],
            'reduced' => [],
            'remitted' => [],
        ];

        $feeProducts = [
            'regular' => Product::query()->findOrFail(Config::integer('omnomcom.fee.regular')),
            'reduced' => Product::query()->findOrFail(Config::integer('omnomcom.fee.reduced')),
            'remitted' => Product::query()->findOrFail(Config::integer('omnomcom.fee.remitted')),
        ];
        $usersToCharge->chunkById(100, function ($users) use ($feeProducts, $charged) {
            /** @var User $user */
            foreach ($users as $user) {
                $product = null;
                $fee_type = null;
                $email_remittance_reason = null;

                switch ($user->member->membership_type) {
                    case MembershipTypeEnum::DONOR:
                        $fee_type = 'remitted';
                        $email_remittance_reason = 'you are a donor of the association, and your donation is not handled via the membership fee system';
                        $charged->remitted[] = $user->name.' (#'.$user->id.') - Donor';
                        break;
                    case MembershipTypeEnum::HONORARY:
                        $fee_type = 'remitted';
                        $email_remittance_reason = 'you are an honorary member';
                        $charged->remitted[] = $user->name.' (#'.$user->id.') - Honorary Member';
                        break;
                    case MembershipTypeEnum::LIFELONG:
                        $fee_type = 'remitted';
                        $email_remittance_reason = 'you signed up for life-long membership when you became a member';
                        $charged->remitted[] = $user->name.' (#'.$user->id.') - Lifelong Member';
                        break;
                    case MembershipTypeEnum::PET:
                        $fee_type = 'remitted';
                        $email_remittance_reason = 'you are a pet and therefore do not possess any money';
                        $charged->remitted[] = $user->name.' (#'.$user->id.') - Pet Member';
                        break;
                    case MembershipTypeEnum::REGULAR:
                        if ($user->member->UtAccount && ! $user->member->is_primary_at_another_association) {
                            $fee_type = 'regular';
                            $charged->regular[] = $user->name.' (#'.$user->id.')';
                        } else {
                            $fee_type = 'reduced';
                            $charged->reduced[] = $user->name.' (#'.$user->id.')';
                        }

                        break;
                    case MembershipTypeEnum::PENDING:
                        throw new Exception('This should not happen as we filter out pending members.');
                }

                $charged->count++;

                $product = $feeProducts[$fee_type];
                /**@phpstan-ignore-next-line */
                if (! $product) {
                    $this->error('No product found for user '.$user->id);

                    continue;
                }

                $product->buyForUser($user, 1, null, null, null, null, 'membership_fee_cron');

                Mail::to($user)->queue((new FeeEmail($user, $fee_type, $product->price, $email_remittance_reason))->onQueue('high'));
            }

            $this->info('Charged '.$charged->count.' of '.Member::query()->whereNot('membership_type', MembershipTypeEnum::PENDING)->count().' members their fee.');
        });

        /** @phpstan-ignore-next-line */
        if ($charged->count > 0) {
            Mail::queue((new FeeEmailForBoard($charged))->onQueue('high'));
        }

        return 0;
    }
}
