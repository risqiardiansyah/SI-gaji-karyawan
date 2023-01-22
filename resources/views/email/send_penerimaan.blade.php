
<!DOCTYPE html>
<html>
    <title>Alan Creative</title>
    <body>
        <p>Hello {{$person}}, </p>
        <p>Berikut kami kirimkan {{$self}}, PT Alan Mediatech Indonesia, dengan nomor {{$transaction}}.</p>
        <p>Detail penerimaan kami sampaikan di dalam surat terlampir.</p>
        <p>{!! nl2br(e($email_draft)) !!}</p>
    </body>