@component('mail::message')
# Email Verification

New user has submitted a contact form with the email {{$details->email}}  and {{ $details->name}}.

{{-- @component('mail::button', ['url' =>route('admin.sendApproveDecision',$user->id),'color'=>'blue'])
  Approve
@endcomponent


@component('mail::button', ['url' => '$url1','color'=>'red'])
  Reject
@endcomponent --}}


Thanks,<br>
{{ config('app.name') }}
@endcomponent
