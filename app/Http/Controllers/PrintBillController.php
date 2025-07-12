<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeeklyBill;
use App\Models\Collector;
use App\Models\Subscriber;

class PrintBillController extends Controller
{
    public function index(Request $request)
    {
        $generatorId = $request->input('generator_id');
        $collectorId = $request->input('collector_id');
        $count = $request->input('count', 10);

        $query = WeeklyBill::with(['subscriber.generator']);

        if ($generatorId) {
            $query->whereHas('subscriber', function ($q) use ($generatorId) {
                $q->where('generator_id', $generatorId);
            });
        } elseif ($collectorId) {
            // جيب الجابي وشوف شو المولدة يلي بيجمعلها
            $collector = Collector::find($collectorId);

            if (! $collector) {
                return response()->json(['error' => 'الجابي غير موجود'], 404);
            }

            $query->whereHas('subscriber', function ($q) use ($collector) {
                $q->where('generator_id', $collector->generator_id);
            });
        } else {
            return response()->json(['error' => 'يجب اختيار جابي أو مولدة'], 422);
        }

        $bills = $query->latest()->take($count)->get();

        return view('print.bills', compact('bills'));
    }
}
