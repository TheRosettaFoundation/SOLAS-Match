Hello{if $user->getDisplayName() == ''},{else} {$user->getDisplayName()},{/if}

You have requested a password reset for your SOLAS Match account. You can reset 
your password by copying the link below and pasting it into the address bar of 
your web browser. If you did not request a password please ignore this email.

{$site_url}
