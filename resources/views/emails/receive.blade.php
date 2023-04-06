@component('mail::message')
# Email Verification

New user has submitted a contact form.

Login into your account to

@component('mail::button', ['url' => '','color'=>'blue'])
  Activate
@endcomponent

               OR

@component('mail::button', ['url' => '','color'=>'red'])
  Reject
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
