<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:120'],
            'email'   => ['required','email'],
            'subject' => ['nullable','string','max:150'],
            'message' => ['required','string','max:4000'],
            'hp'      => ['nullable','max:0'], // honeypot harus kosong
        ], [
            'name.required'    => 'Nama wajib diisi.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'message.required' => 'Pesan wajib diisi.',
        ]);

        // Jika honeypot terisi, anggap bot dan jangan proses
        if ($request->filled('hp')) {
            return back()->withInput()->withErrors(['name' => 'Terjadi kesalahan. Silakan coba lagi.']);
        }

        // Tujuan email (pakai MAIL_FROM_ADDRESS jika CONTACT_TO belum diset)
        $to = config('mail.from.address') ?: env('CONTACT_TO', 'support@ecomart.local');

        Mail::to($to)->send(new ContactMessage(
            $data['name'],
            $data['email'],
            $data['subject'] ?? '',
            $data['message']
        ));

        return back()->with('status', 'Terima kasih! Pesan kamu sudah kami terima.');
    }
}
