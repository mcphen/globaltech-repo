<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Update or set keys in the .env file safely.
     */
    protected function setEnvValues(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }
        $content = file_get_contents($envPath);
        foreach ($data as $key => $value) {
            // Ensure values are properly quoted if they contain spaces or special chars
            $escaped = is_null($value) ? '' : (preg_match('/\s|#|=|"/',$value) ? '"'.str_replace('"','\\"',$value).'"' : $value);
            $pattern = "/^" . preg_quote($key, '/') . "=.*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $key . '=' . $escaped, $content);
            } else {
                $content .= "\n" . $key . '=' . $escaped;
            }
        }
        file_put_contents($envPath, $content);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::all();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Settings/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings',
            'value' => 'nullable|string',
            'group' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'label' => 'required|string|max:255',
        ]);

        Setting::create($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $setting = Setting::findOrFail($id);

        return Inertia::render('Admin/Settings/Show', [
            'setting' => $setting
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $setting = Setting::findOrFail($id);

        return Inertia::render('Admin/Settings/Edit', [
            'setting' => $setting
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $setting = Setting::findOrFail($id);

        $validated = $request->validate([
            'value' => 'nullable|string',
            'group' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'label' => 'required|string|max:255',
        ]);

        $setting->update($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting deleted successfully.');
    }

    /**
     * Display contact settings form.
     */
    public function contactSettings()
    {
        $contactSettings = Setting::where('group', 'contact')->get();

        return Inertia::render('Admin/Settings/Contact', [
            'settings' => $contactSettings
        ]);
    }

    /**
     * Update contact settings.
     */
    public function updateContactSettings(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Contact settings updated successfully.');
    }

    /**
     * Display SMTP (mail) settings form.
     */
    public function mailSettings()
    {
        $mail = [
            'MAIL_MAILER' => env('MAIL_MAILER', 'smtp'),
            'MAIL_HOST' => env('MAIL_HOST', ''),
            'MAIL_PORT' => env('MAIL_PORT', ''),
            'MAIL_USERNAME' => env('MAIL_USERNAME', ''),
            'MAIL_PASSWORD' => env('MAIL_PASSWORD', ''),
            'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION', ''),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', ''),
            'MAIL_FROM_NAME' => env('MAIL_FROM_NAME', config('app.name')),
        ];

        return Inertia::render('Admin/Settings/Mail', [
            'mail' => $mail,
        ]);
    }

    /**
     * Update SMTP settings and persist into .env
     */
    public function updateMailSettings(Request $request)
    {
        $data = $request->validate([
            'MAIL_MAILER' => 'required|in:smtp,sendmail,log,array,failover,mailgun,postmark,ses,ses-v2',
            'MAIL_HOST' => 'required|string',
            'MAIL_PORT' => 'required|integer',
            'MAIL_USERNAME' => 'nullable|string',
            'MAIL_PASSWORD' => 'nullable|string',
            'MAIL_ENCRYPTION' => 'nullable|in:null,,tls,ssl,starttls',
            'MAIL_FROM_ADDRESS' => 'required|email',
            'MAIL_FROM_NAME' => 'required|string',
        ]);

        // Normalize encryption (allow empty to mean null)
        if (isset($data['MAIL_ENCRYPTION']) && ($data['MAIL_ENCRYPTION'] === 'null' || $data['MAIL_ENCRYPTION'] === '')) {
            $data['MAIL_ENCRYPTION'] = 'null';
        }

        // Write to .env
        $this->setEnvValues($data);

        // Update runtime config so it's immediate without cache clear
        config([
            'mail.default' => $data['MAIL_MAILER'],
            'mail.from.address' => $data['MAIL_FROM_ADDRESS'],
            'mail.from.name' => $data['MAIL_FROM_NAME'],
            'mail.mailers.smtp.host' => $data['MAIL_HOST'],
            'mail.mailers.smtp.port' => (int)$data['MAIL_PORT'],
            'mail.mailers.smtp.username' => $data['MAIL_USERNAME'] ?? null,
            'mail.mailers.smtp.password' => $data['MAIL_PASSWORD'] ?? null,
            'mail.mailers.smtp.encryption' => ($data['MAIL_ENCRYPTION'] ?? null) === 'null' ? null : ($data['MAIL_ENCRYPTION'] ?? null),
        ]);

        return redirect()->back()->with('success', 'SMTP settings updated successfully.');
    }
}
