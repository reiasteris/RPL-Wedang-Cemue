<?php
// auth/login.php
?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Pak Resto | Portal Pegawai
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        /* =========================================
           PAGE
           ========================================= */

        body {

            margin: 0;

            min-height: 100vh;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #eef7f8,
                    #f5f7fa 55%,
                    #edf3f8
                );

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px;

            color: #1f2937;

        }


        /* =========================================
           MAIN CARD
           ========================================= */

        .login-wrapper {

            width: 100%;

            max-width: 930px;

            min-height: 530px;

            display: grid;

            grid-template-columns:
                46%
                54%;

            background: #ffffff;

            border-radius: 24px;

            overflow: hidden;

            box-shadow:
                0 25px 60px
                rgba(28, 57, 82, 0.14);

        }


        /* =========================================
           BRAND PANEL
           ========================================= */

        .brand-panel {

            position: relative;

            overflow: hidden;

            background:
                linear-gradient(
                    150deg,
                    #20385f 0%,
                    #285d80 48%,
                    #159c9e 100%
                );

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 45px;

        }


        /* =========================================
           DECORATIVE SHAPES
           ========================================= */

        .shape-one {

            position: absolute;

            width: 320px;

            height: 320px;

            border-radius: 50%;

            background:
                rgba(83, 207, 215, 0.10);

            top: -150px;

            left: -100px;

        }


        .shape-two {

            position: absolute;

            width: 260px;

            height: 260px;

            border-radius: 50%;

            background:
                rgba(255,255,255,0.07);

            right: -130px;

            bottom: -120px;

        }


        .shape-line {

            position: absolute;

            width: 180px;

            height: 180px;

            border-radius: 50%;

            border:
                1px solid
                rgba(255,255,255,0.08);

            right: 40px;

            top: 60px;

        }


        .brand-content {

            position: relative;

            z-index: 2;

            width: 100%;

            text-align: center;

        }


        /* =========================================
           LOGO
           ========================================= */

        .logo-box {

            width: 88px;

            height: 88px;

            margin:
                0 auto 25px;

            border-radius: 22px;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,0.22),
                    rgba(255,255,255,0.08)
                );

            border:
                1px solid
                rgba(255,255,255,0.25);

            box-shadow:
                0 15px 35px
                rgba(0,0,0,0.13);

            backdrop-filter:
                blur(8px);

        }


        .logo-text {

            font-size: 27px;

            font-weight: 900;

            letter-spacing:
                -1px;

        }


        /* =========================================
           BRAND TITLE
           ========================================= */

        .brand-title {

            margin: 0;

            font-size: 40px;

            font-weight: 900;

            letter-spacing:
                -1.5px;

        }


        /* =========================================
           RIGHT FORM
           ========================================= */

        .form-panel {

            display: flex;

            align-items: center;

            justify-content: center;

            padding:
                55px 65px;

            background:
                #ffffff;

        }


        .form-content {

            width: 100%;

            max-width: 370px;

        }


        .login-heading {

            margin: 0;

            font-size: 30px;

            font-weight: 800;

            color:
                #1d2939;

            letter-spacing:
                -0.5px;

        }


        .login-description {

            margin:
                8px 0 34px;

            font-size: 14px;

            color:
                #8a94a6;

        }


        /* =========================================
           ERROR
           ========================================= */

        .error {

            padding:
                12px 14px;

            margin-bottom:
                20px;

            border-radius:
                9px;

            background:
                #fff0f0;

            border:
                1px solid #ffd0d0;

            color:
                #b42318;

            font-size:
                13px;

        }


        /* =========================================
           FORM
           ========================================= */

        .form-group {

            margin-bottom:
                21px;

        }


        label {

            display: block;

            margin-bottom:
                8px;

            font-size:
                12px;

            font-weight:
                700;

            color:
                #536173;

            letter-spacing:
                0.6px;

        }


        .input-wrapper {

            position:
                relative;

        }


        input {

            width: 100%;

            height: 51px;

            padding:
                0 15px;

            border:
                1px solid #dfe5eb;

            border-radius:
                10px;

            background:
                #fafcfd;

            color:
                #1f2937;

            font-size:
                14px;

            transition:
                0.2s;

        }


        input:hover {

            border-color:
                #c7d1dc;

        }


        input:focus {

            outline: none;

            border-color:
                #159c9e;

            background:
                white;

            box-shadow:
                0 0 0 4px
                rgba(21,156,158,0.10);

        }


        input::placeholder {

            color:
                #a6afba;

        }


        .password-input {

            padding-right:
                90px;

        }


        /* =========================================
           PASSWORD TOGGLE
           ========================================= */

        .toggle-password {

            position:
                absolute;

            right:
                10px;

            top:
                50%;

            transform:
                translateY(-50%);

            border:
                none;

            background:
                transparent;

            color:
                #159c9e;

            font-size:
                12px;

            font-weight:
                bold;

            cursor:
                pointer;

            padding:
                5px;

        }


        .toggle-password:hover {

            color:
                #116f71;

        }


        /* =========================================
           LOGIN BUTTON
           ========================================= */

        .login-button {

            width:
                100%;

            height:
                52px;

            margin-top:
                5px;

            border:
                none;

            border-radius:
                10px;

            background:
                linear-gradient(
                    100deg,
                    #243b64,
                    #245c7c,
                    #159c9e
                );

            background-size:
                200% 100%;

            color:
                white;

            font-size:
                14px;

            font-weight:
                bold;

            letter-spacing:
                0.6px;

            cursor:
                pointer;

            transition:
                0.25s;

            box-shadow:
                0 9px 20px
                rgba(30,105,126,0.20);

        }


        .login-button:hover {

            background-position:
                100% 0;

            transform:
                translateY(-1px);

            box-shadow:
                0 12px 24px
                rgba(30,105,126,0.27);

        }


        .login-button:active {

            transform:
                translateY(0);

        }


        /* =========================================
           RESPONSIVE
           ========================================= */

        @media (max-width: 760px) {

            body {

                padding:
                    18px;

            }


            .login-wrapper {

                max-width:
                    430px;

                grid-template-columns:
                    1fr;

                min-height:
                    auto;

            }


            .brand-panel {

                min-height:
                    280px;

                padding:
                    35px 25px;

            }


            .brand-title {

                font-size:
                    32px;

            }


            .form-panel {

                padding:
                    38px 30px;

            }

        }


        @media (max-width: 420px) {

            body {

                padding:
                    10px;

            }


            .login-wrapper {

                border-radius:
                    17px;

            }


            .brand-panel {

                min-height:
                    245px;

            }


            .logo-box {

                width:
                    70px;

                height:
                    70px;

                border-radius:
                    18px;

            }


            .logo-text {

                font-size:
                    22px;

            }


            .brand-title {

                font-size:
                    28px;

            }


            .form-panel {

                padding:
                    30px 22px;

            }

        }

    </style>

</head>


<body>


<div class="login-wrapper">


    <!-- =====================================
         BRAND PANEL
         ===================================== -->

    <section class="brand-panel">


        <div class="shape-one"></div>

        <div class="shape-two"></div>

        <div class="shape-line"></div>


        <div class="brand-content">


            <div class="logo-box">

                <div class="logo-text">

                    PR

                </div>

            </div>


            <h1 class="brand-title">

                PAK RESTO 
                UNIKOM

            </h1>


        </div>


    </section>



    <!-- =====================================
         LOGIN PANEL
         ===================================== -->

    <section class="form-panel">


        <div class="form-content">


            <h2 class="login-heading">

                MASUK KE SISTEM

            </h2>


            <p class="login-description">

                Gunakan akun pegawai Anda untuk melanjutkan.

            </p>



            <!-- ERROR -->

            <?php if (isset($_GET['error'])): ?>


                <div class="error">


                    <?php

                    if (
                        $_GET['error']
                        == 'invalid'
                    ) {

                        echo
                            "Username atau password salah.";

                    }

                    elseif (
                        $_GET['error']
                        == 'empty'
                    ) {

                        echo
                            "Username dan password wajib diisi.";

                    }

                    ?>


                </div>


            <?php endif; ?>



            <!-- FORM -->

            <form
                action="authenticate.php"
                method="POST"
            >


                <!-- USERNAME -->

                <div class="form-group">


                    <label
                        for="username"
                    >

                        Username

                    </label>


                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        required
                    >


                </div>



                <!-- PASSWORD -->

                <div class="form-group">


                    <label
                        for="password"
                    >

                        Password

                    </label>


                    <div class="input-wrapper">


                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="password-input"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >


                        <button
                            type="button"
                            class="toggle-password"
                            id="toggle-password"
                        >

                            Tampilkan

                        </button>


                    </div>


                </div>



                <!-- LOGIN BUTTON -->

                <button
                    type="submit"
                    class="login-button"
                >

                    MASUK
                    &nbsp; →

                </button>


            </form>


        </div>


    </section>


</div>



<script>

    const passwordInput =
        document.getElementById(
            "password"
        );


    const togglePassword =
        document.getElementById(
            "toggle-password"
        );


    togglePassword.addEventListener(
        "click",
        function () {


            if (
                passwordInput.type
                === "password"
            ) {

                passwordInput.type =
                    "text";

                togglePassword.textContent =
                    "Sembunyikan";

            }

            else {

                passwordInput.type =
                    "password";

                togglePassword.textContent =
                    "Tampilkan";

            }

        }
    );

</script>


</body>

</html>