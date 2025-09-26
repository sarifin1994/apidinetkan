<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="Radiusqu" />
    <meta name="author" content="Putra Garsel Interkoneksi" />
    <title>Pay Now | {{ config('app.name') }} Radius</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/radiusqu/img/favicon.png') }}" />
    <link href="{{ asset('assets/sbpro/css/styles.css') }}" rel="stylesheet" />

<style>
.badan{
	width:100vw;
	height:100vh;
	display:flex;
	align-items:center;
	justify-content:center;
	background:radial-gradient(circle at 75% 50%, #BFDCE5 25%, #F5E9CF 75%);
} 
button.btn {
	background-color:#2B3467;
	color:white;
	height:42px;
}
button.btn:hover{
	background-color:#3E54AC;
	color:white;
	height:42px;
}
.modal-header {
  background-color: #2B3467;
  color: white;
}
.modal-header > button{
  color: white;
}
label.radio-card {
  cursor: pointer;
  margin: .5em;
}
label.radio-card .card-content-wrapper {
  background: #fff;
  border-radius: 5px;
  padding: 15px;
  box-shadow: 0 2px 4px 0 rgba(219, 215, 215, 0.04);
  transition: 200ms linear;
  position: relative;
	 min-width: 170px;
}
label.radio-card .check-icon {
  width: 20px;
  height: 20px;
  display: inline-block;
  border-radius: 50%;
  transition: 200ms linear;
  position: absolute;
  right: -10px;
  top: -10px;
}
label.radio-card .check-icon:before {
  content: "";
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='12' height='9' viewBox='0 0 12 9' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0.93552 4.58423C0.890286 4.53718 0.854262 4.48209 0.829309 4.42179C0.779553 4.28741 0.779553 4.13965 0.829309 4.00527C0.853759 3.94471 0.889842 3.88952 0.93552 3.84283L1.68941 3.12018C1.73378 3.06821 1.7893 3.02692 1.85185 2.99939C1.91206 2.97215 1.97736 2.95796 2.04345 2.95774C2.11507 2.95635 2.18613 2.97056 2.2517 2.99939C2.31652 3.02822 2.3752 3.06922 2.42456 3.12018L4.69872 5.39851L9.58026 0.516971C9.62828 0.466328 9.68554 0.42533 9.74895 0.396182C9.81468 0.367844 9.88563 0.353653 9.95721 0.354531C10.0244 0.354903 10.0907 0.369582 10.1517 0.397592C10.2128 0.425602 10.2672 0.466298 10.3112 0.516971L11.0651 1.25003C11.1108 1.29672 11.1469 1.35191 11.1713 1.41247C11.2211 1.54686 11.2211 1.69461 11.1713 1.82899C11.1464 1.88929 11.1104 1.94439 11.0651 1.99143L5.06525 7.96007C5.02054 8.0122 4.96514 8.0541 4.90281 8.08294C4.76944 8.13802 4.61967 8.13802 4.4863 8.08294C4.42397 8.0541 4.36857 8.0122 4.32386 7.96007L0.93552 4.58423Z' fill='white'/%3E%3C/svg%3E%0A");
  background-repeat: no-repeat;
  background-size: 12px;
  background-position: center center;
  transform: scale(1.6);
  transition: 200ms linear;
  opacity: 0;
}
label.radio-card input[type=radio] {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
}
label.radio-card input[type=radio]:checked + .card-content-wrapper {
  box-shadow: 0 2px 4px 0 rgba(219, 215, 215, 0.5), 0 0 0 2px #3057d5;
}
label.radio-card input[type=radio]:checked + .card-content-wrapper .check-icon {
  background: #3057d5;
  border-color: #3057d5;
  transform: scale(1.2);
}
label.radio-card input[type=radio]:checked + .card-content-wrapper .check-icon:before {
  transform: scale(1);
  opacity: 1;
}
label.radio-card input[type=radio]:focus + .card-content-wrapper .check-icon {
  box-shadow: 0 0 0 4px rgba(48, 86, 213, 0.2);
  border-color: #3056d5;
}
label.radio-card .card-content img {
  margin-bottom: 10px;
}
label.radio-card .card-content h4 {
  font-size: 16px;
  letter-spacing: -0.24px;
  text-align: center;
  color: #1f2949;
		margin: 0;
}
label.radio-card .card-content h5 {
  font-size: 14px;
  line-height: 1.4;
  text-align: center;
  color: #686d73;
}
.card-content > img{
	max-height:35px;
}
.modal-footer > button{
	width:50%;
	height:50px;
	border:0;
	color:#222;
}
.btn-outline-primary:hover {
  color: #fff;
  background-color: #2B3467!important;
}

.btn-outline-light:hover {
  color: #cecece!important;
}


    </style>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-xl px-4">
                    <div class="row justify-content-center">
                        <div class="col-xl-5 col-lg-6 col-md-8 col-sm-11">
                            <!-- Social login form-->
                            <div class="card my-5">
                                <div class="card-body text-center">
                                    <img src="https://img.freepik.com/premium-vector/online-mobile-payment-banking-service-concept-payment-approved-payment-done-vector-illustration-flat-design-web-banner-mobile-app_98702-1311.jpg"
                                        width="200px" />
                                </div>
                                <hr class="my-0" />
                                <div class="container-xl">
                                    <div class="card-body px-0">
                                        <!-- Payment method 1-->
                                        <div class="p-2 mb-4">
                                            <div class="text-center h5">Checkout Berhasil <i
                                                    class="fas fa-check-circle text-green"></i></div>
                                            <div class="small text-center text-muted mb-2">Transfer sesuai nominal
                                                dibawah ini</div>
                                            <div class="h3 text-center">
                                                Rp{{ number_format($invoice->price, 0, '.', '.') }}
                                            </div>
                                        </div>

                                       

                                        

                                        <div class="text-center mt-4"><a href="https://wa.wizard.id/c516ee"
                                                target="_blank" class="btn btn-primary"><i
                                                    class="fab fa-whatsapp"></i>&nbspKonfirmasi Pembayaran</a></div>

                                    </div>
                                </div>
                                <hr class="my-0" />
                                <div class="card-body px-5 py-4">
                                    <div class="small text-center">
                                        Cek status pembayaran dan lisensi
                                        <a href="/">Login ke dashboard!</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="footer-admin mt-auto footer-dark">
                <div class="container-xl px-4">
                    <div class="row">
                        <div class="col-md-6 small">Copyright &copy; Radiiusqu Radius | <a
                                href="https://radiusqu.com" target="_blank">Putra Garsel Interkoneksi</a></div>
                        <div class="col-md-6 text-md-end small">
                            Made with ♥ by <a href="https://radiusqu.com/" target="_blank">Radiusqu Radius</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>

    </script>
</body>

</html>
