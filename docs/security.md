# Security: Frequently Asked Questions

This document will attempt to answer questions that are frequently asked about
Postmill.

## How are passwords stored?

Passwords are hashed using the [bcrypt][bcrypt] algorithm. It incorporates a
randomly generated salt and is costly to bruteforce, making it ideal for secure
password hashing.

Internally, Postmill uses PHP's [`password_hash()`][password-hash] function to
perform the actual hashing.

## Why is there a maximum password length?

Due to a limitation of the Blowfish cipher used by bcrypt, the maximum password
length is 72 bytes. This limitation hasn't stopped the notoriously
security-focused OpenBSD from adopting bcrypt, so it's probably good enough for
us, too.

## Are logins rate-limited?

Yes! By default, Postmill will rate-limit login attempts from every IP address.

## Is two-factor authentication available?

No. We used to have an implementation of it, but it suffered from severe
technical problems. Reimplementing this feature is planned.

## Why is there [an admitted vulnerability][timing-attack] present in the source code?

In some online communities, a few clueless people have been presenting the
following code snippet, taken from Postmill's source code, as evidence that
Postmill has a severe security problem:

~~~php
// TODO - this is susceptible to timing attacks.
// TODO - send only one email with all the links.
foreach ($ur->lookUpByEmail($email) as $user) {
    $mailer->mail($user, $request);
}
~~~

So what's going on here?

This is a piece of the code that's used for password resetting. When the user
enters an email address and hits 'reset password', any user accounts with that
email address are looked up in the database and stored in a list. The code above
iterates over that list and queues an outgoing email for each user account that
was found. (Postmill allows several accounts registered to one email address.)

To protect user privacy, Postmill will not confirm if any emails were actually
sent out. If we did this, an attacker interested to see if anyone had registered
with the given email address would be able to find out by abusing the password
reset feature.

The vulnerability is that under special conditions, it *might* be possible to
tell if an email was sent or not. This is because Postmill must perform a few
extra function calls in the event that there's at least one user account
matching the email address, which takes a few microseconds longer. Thus, in
theory, an attacker looking to find out if an email address is registered could
do so by looking for a discrepancy in the response times between sending a reset
request to their target address, and sending a request to a bogus address. This
is the *timing attack* that is referred to in the source code.

Unfortunately for the attacker, measuring this discrepancy would take hundreds
or thousands of requests, which would alert the target by filling up their email
inbox. Additionally, the password reset has CAPTCHA, and ultimately the timing
attack isn't very reliable anyway, due to the miniscule time differences that
are also affected by server load.

Due to the infeasibility of the attack, the limited circumstances in which
someone would get into trouble for having registered with their email address on
a Postmill instance, and the fact that major web services don't even provide
email privacy at all, it was decided to leave the code as-is with a comment
noting its theoretical possibility. It is not the major, privacy-breaching
vulnerability the rumours have presented it as.


[bcrypt]: https://en.wikipedia.org/wiki/Bcrypt
[password-hash]: https://secure.php.net/manual/en/function.password-hash.php
[timing-attack]: https://gitlab.com/postmill/Postmill/blob/master/src/Controller/ResetPasswordController.php
