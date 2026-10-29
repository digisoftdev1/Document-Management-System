<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = Setting::first();

        return view('settings.website', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'website_title' => ['required', 'string', 'max:255'],
            'website_logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
        ]);

        $setting = Setting::firstOrCreate([]);

        if ($request->hasFile('website_logo')) {
            if ($setting->website_logo) {
                Storage::disk('public')->delete($setting->website_logo);
            }

            $setting->website_logo = $request
                ->file('website_logo')
                ->store('settings/logo', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                Storage::disk('public')->delete($setting->favicon);
            }

            $setting->favicon = $request
                ->file('favicon')
                ->store('settings/favicon', 'public');
        }

        $setting->website_title = $request->website_title;
        $setting->save();

        return back()->with(
            'success',
            'Website settings updated successfully.'
        );
    }

    public function deleteLogo()
    {
        $setting = Setting::firstOrFail();

        if ($setting->website_logo) {
            Storage::disk('public')->delete($setting->website_logo);

            $setting->website_logo = null;
            $setting->save();
        }

        return back()->with(
            'success',
            'Website logo deleted successfully.'
        );
    }

    public function deleteFavicon()
    {
        $setting = Setting::firstOrFail();

        if ($setting->favicon) {
            Storage::disk('public')->delete($setting->favicon);

            $setting->favicon = null;
            $setting->save();
        }

        return back()->with(
            'success',
            'Favicon deleted successfully.'
        );
    }
}