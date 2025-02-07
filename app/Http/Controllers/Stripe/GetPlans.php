<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe;

class GetPlans extends Controller
{
    protected $plans;

    public function __construct()
    {
        Stripe\Stripe::setApiKey(\Config::get('services.stripe.secret'));
        $this->plans = Stripe\Plan::all();
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $result = [];
        foreach ($this->plans as $plan) {
            // $row = (array) $plan;
            $row ['id'] = $plan->id;
            $row['amount'] = $plan->amount;
            $row['nickname'] = $plan->nickname;
            switch ($plan->nickname) {
                case 'UTY':
                    $row['title'] = 'Unlimited trees yearly.';
                    break;
                case 'UTM':
                    $row['title'] = 'Unlimited trees monthly.';
                    break;
                case 'TTY':
                    $row['title'] = 'Ten trees yearly.';
                    break;
                case 'TTM':
                    $row['title'] = 'Ten trees monthly.';
                    break;
                case 'OTY':
                    $row['title'] = 'One tree yearly.';
                    break;
                case 'OTM':
                    $row['title'] = 'One tree monthly.';
                    break;
            }
            $row['subscribed'] = false;
            $result[] = $row;
        }

        return $result;
    }
}
