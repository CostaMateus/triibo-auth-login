<style>
    .title-optin {
        margin: 0 0 10px !important;
        font-weight: 650 !important;
    }

    /* Helpers error */
    .error-border {
        border-color: #D45757 !important;
    }

    .bg-transp {
        background-color: transparent !important;
    }

    .d-none {
        display: none !important;
    }

    .d-flex {
        display: flex !important;
    }

    .text-left {
        text-align: left !important;
    }

    .text-center {
        text-align: center !important;
    }

    .text-danger {
        color: #d45757 !important;
        font-weight: bold;
    }

    .text-dark {
        color: #343a40 !important;
        font-weight: bold;
    }

    .text-muted {
        color: #6C757D !important;
    }

    .small {
        font-size: 0.75em; /* 0.875em; */
    }

    .float-left {
        float: left !important;
    }

    .float-right {
        float: right !important;
    }

    .pt-8 {
        padding-top: 8px !important;
    }

    .pt-20 {
        padding-top: 20px !important;
    }

    .pb-20 {
        padding-bottom: 20px !important;
    }

    .mt-20 {
        margin-top: 20px !important;
    }

    .mb-0 {
        margin-bottom: 0 !important;
    }

    .mb-5 {
        margin-bottom: 5px !important;
    }

    .mb-15 {
        margin-bottom: 15px !important;
    }
    .mx-auto {
        margin-left: auto !important;
        margin-right: auto !important;
    }

    .pointer {
        cursor: pointer;
    }

    /* css comum entre, .bg-modal-load-next-page e .bg-modal-load-checkout */
    .bg-modal-login, .bg-modal-lbc {
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        display: flex;
        position: fixed;
        overflow: auto;
        z-index: 99999999;
        text-align: center;
        justify-content: space-between;
    }
    .bg-modal-login {
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.7);
    }
    .bg-modal-lbc {
        background-color: rgb(255, 255, 255);
    }

    /* modal de centralização, texto e load-gif - checkout */
    .bg-modal-login-child {
        width: 80%;
        margin: auto;
        border-radius: 5px;
        background-color: #FEFEFE;
        border-radius: 5px;
        background-color: #FFF;
        padding: 35px 30px;
        border: 1px solid #E8E8E8;
    }

    .bg-modal-login-child h2 {
        text-align: left;
        margin: 0 0 25px 0 !important;
    }

    .bg-modal-login-child .modal-header {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .bg-modal-login-child .modal-header span {
        font-size: 20px;
        cursor: pointer;
    }

    /* .bg-modal-login-child p { margin-bottom: 0 !important; } */
    .bg-modal-login-child .button {
        width: 100%;
        font-size: 16px;
        margin-top: 20px !important;
        border-radius: 3px;
        height: 53px;
        line-height: 53px;
    }

    /* tamanho do checchout-child de acordo com a tela */
    @media (min-width: 576px) {
        .bg-modal-login-child {
            width: 60%;
        }
    }
    @media (min-width: 768px) {
        .bg-modal-login-child {
            width: 40%;
        }
    }
    @media (min-width: 992px) {
        .bg-modal-login-child {
            max-width: 430px;
        }
    }

    .set-spin {
        display: flex !important;
        justify-content: center;
    }

    .spin-load {
        width: 20px;
        height: 20px;
        margin: auto 5px;
        border: 3px solid #F2F2F2;
        border-top: 3px solid #AEAEAE;
        border-radius: 50%;
        -webkit-animation: spin 1s linear infinite;
        /* Safari */
        animation: spin 1s linear infinite;
    }

    @-webkit-keyframes spin {
        0%   { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
