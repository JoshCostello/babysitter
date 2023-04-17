<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateEarningsRequest;

class CalculatorController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CalculateEarningsRequest $request)
    {
        try {
            return view('welcome', [
                'earnings' => $request->calculate(),
            ]);
        } catch (\Exception $exception) {
            return back()->withInput()
                ->withErrors([
                    'calculation' => $exception->getMessage(),
                ]);
        }
    }
}
