
<!DOCTYPE html>
<html>
    <title>Alan Creative</title>
    <body>
        <p>Hello {{$person}}, </p>
        <p>Berikut kami kirimkan {{$self}}, PT Alan Mediatech Indonesia.</p>
        <p>Detail lebih lanjut kami sampaikan di dalam surat terlampir.</p>
        <p>{!! nl2br(e($email_draft)) !!}</p>
    </body>