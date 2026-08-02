<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Upload;
use App\Models\Setting;

final class SettingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireRole('admin');
    }

    public function index(): string
    {
        if ($this->request->method() === 'POST') {
            $map = [
                'site_name'        => 'general',
                'tagline'          => 'general',
                'contact_email'    => 'general',
                'footer_copyright' => 'general',
                'maintenance_mode' => 'general',
                'meta_title'       => 'seo',
                'meta_description' => 'seo',
                'theme_color'      => 'appearance',
                'dark_mode'        => 'appearance',
                'social_facebook'  => 'social',
                'social_twitter'   => 'social',
                'social_instagram' => 'social',
                'social_youtube'   => 'social',
                'smtp_host'        => 'mail',
                'smtp_port'        => 'mail',
                'smtp_user'        => 'mail',
            ];
            foreach ($map as $key => $group) {
                if ($key === 'maintenance_mode' || $key === 'dark_mode') {
                    Setting::put($key, isset($_POST[$key]) ? '1' : '0', $group);
                    continue;
                }
                if (array_key_exists($key, $_POST)) {
                    Setting::put($key, trim((string) $_POST[$key]), $group);
                }
            }

            foreach (['logo' => 'banners', 'favicon' => 'banners'] as $field => $dir) {
                if (!empty($_FILES[$field]['name'])) {
                    if ($path = Upload::image($_FILES[$field], $dir)) {
                        Setting::put($field, $path, 'appearance');
                    }
                }
            }

            Session::flash('success', 'Settings updated.');
            redirect('admin/settings');
        }

        return $this->view('admin/settings/index', [
            'settings' => Setting::allAsArray(),
        ], 'admin/layouts/app');
    }
}
