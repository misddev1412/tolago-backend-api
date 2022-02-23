<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            'site_name' => 'Laravel Ecommerce',
            'site_email' => 'misddev@gmail.com',
            'site_phone' => '+923335555555',
            'site_address' => 'Lahore, Pakistan',
            'site_description' => 'Laravel Ecommerce',
            'site_keywords' => 'Laravel Ecommerce',
            'site_status' => '1',
            'site_version' => '1.0.0',
            'site_logo' => '',
            'site_favicon' => '',
            'site_copyright' => 'Laravel Ecommerce',
            'site_facebook' => '',
            'site_twitter' => '',
            'site_instagram' => '',
            'site_linkedin' => '',
            'site_google' => '',
            'site_youtube' => '',
            'site_pinterest' => '',
            'site_vimeo' => '',
            'site_tumblr' => '',
            'site_skype' => '',
            'site_whatsapp' => '',
            'site_paypal' => '',
            'site_stripe' => '',
            'site_cod' => '',
            'site_paystack' => '',
            'site_paytm' => '',
            'site_razorpay' => '',
            'site_paypal_client_id' => '',
            'site_paypal_secret' => '',
            'site_stripe_secret' => '',
            'site_stripe_publishable' => '',
            'site_cod_secret' => '',
            'site_paystack_secret' => '',
            'site_paystack_public' => '',
            'site_paytm_merchant' => '',
            'site_paytm_secret' => '',
            'site_razorpay_key' => '',
            'site_razorpay_secret' => '',
            'site_razorpay_currency' => '',
            'email_driver' => 'smtp',
            'email_host' => 'smtp.gmail.com',
            'email_port' => '587',
            'email_username' => '',
            'email_password' => '',
            'email_encryption' => 'tls',
            'email_from_address' => '',
            'email_from_name' => '',
            'sms_driver' => 'nexmo',
            'sms_username' => '',
            'sms_password' => '',
            'sms_from' => '',
            'enable_cache' => false,
            'cache_driver' => 'database',
            'cache_host' => '',
            'cache_port' => '',
            'cache_password' => '',
            'cache_database' => '',
            'cache_prefix' => '',
            'cache_lifetime' => '',
            'enable_registration' => true,
            'maintainance_mode' => false,
            'meta_title' => 'Laravel Ecommerce',
            'meta_description' => 'Laravel Ecommerce',
            'meta_keywords' => 'Laravel Ecommerce',
            'meta_author' => 'Laravel Ecommerce',
            'meta_copyright' => 'Laravel Ecommerce',
            'meta_robots' => 'index, follow',
            'meta_revisit_after' => '7 days',
            'meta_distribution' => 'global',
            'meta_rating' => 'general',
            'meta_language' => 'en',
            'admin_password' => 'secret',
            'enable_admin_2_step_verification' => false,
            'enable_login_captcha' => false,
            'enable_registration_captcha' => false,
            'enable_forgot_password_captcha' => false,
            'enable_admin_captcha' => false
        ];

        foreach ($settings as $key => $value) {
            if (Setting::where('name', $key)->count() == 0) {
                Setting::create([
                    'name' => $key,
                    'value' => $value,
                    'description' => '',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
