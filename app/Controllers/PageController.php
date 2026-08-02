<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Core\RateLimiter;
use App\Core\Database;
use App\Models\Subscriber;

final class PageController extends Controller
{
    public function subscribe(): string
    {
        $email = trim((string) $this->request->input('email', ''));
        $isAjax = $this->request->isAjax();

        if (RateLimiter::tooMany('sub:' . $this->request->ip(), 10, 3600)) {
            return $this->respond($isAjax, false, 'Too many attempts. Try again later.', 'newsletter');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respond($isAjax, false, 'Please enter a valid email address.', 'newsletter');
        }

        $added = (new Subscriber())->subscribe($email, hash('sha256', $this->request->ip()));
        $msg = $added ? 'Subscribed! Watch your inbox for the best deals.' : 'You are already subscribed.';
        return $this->respond($isAjax, true, $msg, 'newsletter');
    }

    public function contact(): string
    {
        if ($this->request->method() === 'POST') {
            if (RateLimiter::tooMany('contact:' . $this->request->ip(), 5, 3600)) {
                Session::flash('error', 'Too many messages. Please try again later.');
                redirect('contact');
            }
            $v = new Validator($this->request->all());
            if (!$v->validate([
                'name'    => 'required|max:120',
                'email'   => 'required|email',
                'message' => 'required|min:10|max:3000',
            ])) {
                Session::flash('error', $v->firstError());
                redirect('contact');
            }
            Database::run(
                'INSERT INTO contact_messages (name, email, subject, message, ip_hash) VALUES (?, ?, ?, ?, ?)',
                [
                    trim((string) $this->request->input('name')),
                    trim((string) $this->request->input('email')),
                    trim((string) $this->request->input('subject', '')),
                    trim((string) $this->request->input('message')),
                    hash('sha256', $this->request->ip()),
                ]
            );
            Session::flash('success', 'Thanks for reaching out — we will reply soon.');
            redirect('contact');
        }

        $this->seo(['title' => 'Contact Us | ' . config('app.name'), 'canonical' => url('contact')]);
        return $this->view('pages/contact', []);
    }

    public function about(): string
    {
        $this->seo(['title' => 'About Us | ' . config('app.name'), 'canonical' => url('about')]);
        return $this->view('pages/static', ['heading' => 'About ToolzyNet', 'slug' => 'about']);
    }

    public function privacy(): string
    {
        $this->seo(['title' => 'Privacy Policy | ' . config('app.name'), 'canonical' => url('privacy-policy')]);
        return $this->view('pages/static', ['heading' => 'Privacy Policy', 'slug' => 'privacy']);
    }

    public function disclosure(): string
    {
        $this->seo(['title' => 'Affiliate Disclosure | ' . config('app.name'), 'canonical' => url('disclosure')]);
        return $this->view('pages/static', ['heading' => 'Affiliate Disclosure', 'slug' => 'disclosure']);
    }

    public function terms(): string
    {
        $this->seo(['title' => 'Terms of Service | ' . config('app.name'), 'canonical' => url('terms')]);
        return $this->view('pages/static', ['heading' => 'Terms of Service', 'slug' => 'terms']);
    }

    private function respond(bool $isAjax, bool $ok, string $message, string $redirectTo): string
    {
        if ($isAjax) {
            return $this->json(['ok' => $ok, 'message' => $message]);
        }
        Session::flash($ok ? 'success' : 'error', $message);
        redirect('/');
    }
}
