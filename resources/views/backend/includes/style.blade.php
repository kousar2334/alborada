<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet"
    href="{{ asset('public/web-assets/backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/web-assets/backend/css/adminlte.css') }}">
<link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/web-assets/backend/css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('public/web-assets/backend/plugins/dropzone/min/dropzone.min.css') }}">
<style>
    .lds-ellipsis {
        display: inline-block;
        position: relative;
        width: 70px;
        height: 13px;
    }

    .lds-ellipsis div {
        position: absolute;
        top: 0px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: #fff;
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }

    .lds-ellipsis div:nth-child(1) {
        left: 8px;
        animation: lds-ellipsis1 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(2) {
        left: 8px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(3) {
        left: 32px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(4) {
        left: 56px;
        animation: lds-ellipsis3 0.6s infinite;
    }

    @keyframes lds-ellipsis1 {
        0% {
            transform: scale(0);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes lds-ellipsis3 {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(0);
        }
    }

    @keyframes lds-ellipsis2 {
        0% {
            transform: __tr(0, 0);
        }

        100% {
            transform: __tr(24px, 0);
        }
    }

    .dropzone {
        border: 1px dashed rgba(0, 0, 0, .3) !important;
        border-radius: 10px;
    }

    .gap-10 {
        gap: 10px;
    }
</style>
