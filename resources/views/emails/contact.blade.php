@component('mail::message')
# Pesan Baru dari Form Kontak

**Nama:** {{ $name }}
**Email:** {{ $email }}
**Subjek:** {{ $subjectLine ?: '-' }}

---

{!! nl2br(e($bodyText)) !!}

@endcomponent
