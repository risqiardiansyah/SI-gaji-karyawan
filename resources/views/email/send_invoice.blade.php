
<!DOCTYPE html>
<html>
    <body>
        <p>Hello {{$person}}, </p>
        <p>Berikut tagihan dari order {{$self}}, dengan nomor invoice {{$transaction}}.</p>
        <p>{!! nl2br(e($email_draft)) !!}</p>
    </body>