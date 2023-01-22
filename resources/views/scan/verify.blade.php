<!doctype html>
<html>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Alan Creative - Barcode Scan Verification</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='' rel='stylesheet'>
    <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/favicon.ico') }}">
    <style>
        .card {
            width: 400px;
            padding: 10px;
            border-radius: 20px;
            background: #fff;
            border: none;
            height: auto;
            position: relative
        }

        .container {
            height: 100vh
        }

        body {
            background: #eee
        }

        .mobile-text {
            color: #2b2b2bb8;
            font-size: 15px
        }

        .form-control {
            margin-right: 12px
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #ff8880;
            outline: 0;
            box-shadow: none
        }

        .cursor {
            cursor: pointer
        }

        .noHover {
            pointer-events: none;
        }

        .hover {
            pointer-events: visible !important;
        }

    </style>
</head>

<body oncontextmenu='return false' class='snippet-body noHover'>
    <div class="d-flex justify-content-center align-items-center container">
        <div class="card py-5 px-3">
            <center>
                <img src="{{ asset('storage/img/alan_icon.png') }}" alt="Logo" width="150">
            </center>
            <div class="text-center mb-3">
                @if ($code == '')
                    <iframe src="https://embed.lottiefiles.com/animation/92138" width="300" height="300"></iframe>
                    {{-- No One --}}
                    <h5 class="m-0">Harap Scan Barcode Dengan Kamera Ponsel Anda Untuk Memvalidasi Surat Alan Creative.
                    </h5>
                @elseif (!isset($user))
                    <iframe src="https://embed.lottiefiles.com/animation/58412" width="300" height="300"></iframe>
                    {{-- Not Verified --}}
                    <h5 class="m-0">Surat Tidak Ditemukan.</h5>
                @else
                    <iframe src="https://embed.lottiefiles.com/animation/78619" width="300" height="300"></iframe>
                    <h5 class="mb-4">Surat Tervalidasi !</h5>

                    <span class="mobile-text text-center mt-5">
                        <center><small>Ditandatangani oleh</small></center>
                        <table class="w-100">
                            @if ($type == 'signer')
                                <tr>
                                    <td><b>{{ $user->name }}</b></td>
                                </tr>
                                <tr>
                                    <td>{{ $user->position }}</td>
                                </tr>
                            @elseif($type == 'users')
                                <tr>
                                    <td><b>{{ $user->karyawan_nama == '' ? $user->name : $user->karyawan_nama }}</b></td>
                                </tr>
                                <tr>
                                    <td>{{ $user->posisi }}</td>
                                </tr>
                            @elseif($type == 'karyawan')
                                <tr>
                                    <td><b>{{ $user->nama }}</b></td>
                                </tr>
                                <tr>
                                    <td>{{ $user->posisi }}</td>
                                </tr>
                            @endif
                        </table>
                    </span>
                @endif
                {{-- Verified --}}
            </div>
            <div class="text-center mt-5">
                <span class="d-block mobile-text">Butuh Bantuan?</span>
                <a href="https://wa.me/6281994999444" style="text-decoration: none"><span
                        class="font-weight-bold text-danger cursor hover">Hubungi Kami</span></a>
            </div>
        </div>
    </div>
    <script type='text/javascript'
        src='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js'></script>
</body>

</html>
