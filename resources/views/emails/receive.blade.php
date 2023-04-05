@component('mail::message')
# Email Verification

New user has submitted a contact form with the email {{$details->email}}  and {{ $details->name}}. 

@component('mail::button', ['url' => '$url1'])
  Approve
  @endcomponent


  @component('mail::button', ['url' => '$url1'])
  Reject
  @endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
