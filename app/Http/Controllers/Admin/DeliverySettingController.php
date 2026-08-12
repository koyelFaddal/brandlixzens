<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliverySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliverySettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.delivery-settings.edit', [
            'settings' => DeliverySetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fixed_delivery_charge' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'free_delivery_minimum' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        DeliverySetting::current()->update($validated);

        return redirect()
            ->route('admin.delivery-settings.edit')
            ->with('status', 'Delivery settings updated successfully.');
    }
}
