<?php

session_start();

if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $usernameBenar = "admin";
    $passwordBenar = "admin123";

    if ($username === $usernameBenar && $password === $passwordBenar) {

        $_SESSION["login"] = true;
        $_SESSION["username"] = $username;

        header("Location: index.php");
        exit;

    } else {

        $error = "Username atau password tidak sesuai.";

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login | Product Information System</title>


<style>

/* ==============================
   RESET
============================== */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


body {

    min-height: 100vh;

    font-family:
        "Segoe UI",
        Arial,
        sans-serif;

    background: #f3efe7;

    color: #292725;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 30px;

    overflow: hidden;

}


/* ==============================
   DECORATION
============================== */

.bg-circle-one {

    position: fixed;

    width: 430px;
    height: 430px;

    border-radius: 50%;

    background: #e7ddcc;

    top: -190px;
    left: -130px;

}


.bg-circle-two {

    position: fixed;

    width: 350px;
    height: 350px;

    border-radius: 50%;

    background: #e4d5bb;

    right: -150px;
    bottom: -130px;

}


.bg-line {

    position: fixed;

    width: 300px;
    height: 300px;

    border-radius: 50%;

    border: 1px solid #d5c5a8;

    left: 7%;
    bottom: 5%;

}


/* ==============================
   LOGIN WRAPPER
============================== */

.login-wrapper {

    width: 1050px;

    max-width: 100%;

    min-height: 620px;

    position: relative;

    z-index: 5;

    display: grid;

    grid-template-columns: 47% 53%;

    background: #fffdf9;

    border-radius: 28px;

    overflow: hidden;

    box-shadow:
        0 30px 70px rgba(42, 38, 31, 0.18);

}


/* ==============================
   LEFT PANEL
============================== */

.visual-panel {

    position: relative;

    background: #292824;

    overflow: hidden;

    padding: 45px;

    color: white;

}


/* gold circle */

.visual-panel::before {

    content: "";

    position: absolute;

    width: 430px;
    height: 430px;

    border-radius: 50%;

    background: #34322d;

    right: -230px;
    top: -100px;

}


/* second circle */

.visual-panel::after {

    content: "";

    position: absolute;

    width: 220px;
    height: 220px;

    border-radius: 50%;

    border: 1px solid rgba(199,150,69,0.35);

    left: -100px;
    bottom: 30px;

}


/* ==============================
   BRAND
============================== */

.brand {

    position: relative;

    z-index: 5;

}


.logo {

    width: 50px;
    height: 50px;

    border-radius: 14px;

    background: #c79645;

    color: #292824;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 24px;

    margin-bottom: 22px;

}


.brand small {

    display: block;

    color: #c79645;

    font-size: 9px;

    font-weight: 700;

    letter-spacing: 2px;

    margin-bottom: 8px;

}


.brand h1 {

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 27px;

    font-weight: 500;

    line-height: 1.25;

}


/* ==============================
   VISUAL TEXT
============================== */

.visual-text {

    position: relative;

    z-index: 5;

    margin-top: 105px;

}


.visual-text span {

    color: #c79645;

    font-size: 10px;

    font-weight: 700;

    letter-spacing: 2px;

}


.visual-text h2 {

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    font-size: 40px;

    line-height: 1.1;

    font-weight: 500;

    margin-top: 12px;

}


.visual-text h2 em {

    color: #d6b475;

    font-style: normal;

}


.visual-text p {

    max-width: 390px;

    color: #bdb9b1;

    font-size: 12px;

    line-height: 1.8;

    margin-top: 17px;

}


/* ==============================
   FLOWER / PLANT
============================== */

.flower {

    position: absolute;

    z-index: 4;

    right: 55px;

    bottom: 55px;

    font-size: 82px;

    opacity: 0.85;

}


.leaf {

    position: absolute;

    z-index: 4;

    right: 145px;

    bottom: 105px;

    font-size: 42px;

    transform: rotate(-25deg);

}


/* ==============================
   LEFT FOOTER
============================== */

.visual-footer {

    position: absolute;

    z-index: 6;

    bottom: 27px;

    left: 45px;

    color: #858179;

    font-size: 9px;

}


/* ==============================
   RIGHT PANEL
============================== */

.form-panel {

    background: #fffdf9;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 55px;

}


/* ==============================
   FORM BOX
============================== */

.form-box {

    width: 100%;

    max-width: 390px;

}


/* top decoration */

.form-mark {

    width: 42px;
    height: 4px;

    background: #c79645;

    border-radius: 5px;

    margin-bottom: 28px;

}


.form-box .label {

    color: #a77b35;

    font-size: 10px;

    font-weight: 700;

    letter-spacing: 2px;

}


.form-box h2 {

    font-family:
        Georgia,
        "Times New Roman",
        serif;

    color: #302e2a;

    font-size: 36px;

    font-weight: 500;

    margin-top: 8px;

}


.subtitle {

    color: #8d877e;

    font-size: 12px;

    line-height: 1.7;

    margin-top: 10px;

    margin-bottom: 32px;

}


/* ==============================
   ERROR
============================== */

.error {

    background: #f3dfd8;

    color: #914f40;

    border: 1px solid #e4c5bb;

    border-radius: 9px;

    padding: 11px 13px;

    font-size: 11px;

    margin-bottom: 18px;

}


/* ==============================
   INPUT
============================== */

.form-group {

    margin-bottom: 19px;

}


.form-group label {

    display: block;

    color: #4d4841;

    font-size: 11px;

    font-weight: 700;

    margin-bottom: 7px;

}


.input-box {

    position: relative;

}


.input-box span {

    position: absolute;

    left: 15px;

    top: 50%;

    transform: translateY(-50%);

    color: #a27c3d;

    font-size: 14px;

}


.input-box input {

    width: 100%;

    height: 51px;

    border: 1px solid #ded8ce;

    border-radius: 11px;

    background: #f8f5ef;

    color: #332f2a;

    font-family: inherit;

    font-size: 12px;

    outline: none;

    padding: 0 43px;

    transition: 0.2s;

}


.input-box input::placeholder {

    color: #aaa49b;

}


.input-box input:focus {

    background: white;

    border-color: #c79645;

    box-shadow:
        0 0 0 3px #f2e8d5;

}


/* ==============================
   PASSWORD BUTTON
============================== */

.password-button {

    position: absolute;

    right: 13px;

    top: 50%;

    transform: translateY(-50%);

    border: none;

    background: transparent;

    color: #8f887e;

    cursor: pointer;

    font-size: 14px;

}


/* ==============================
   BUTTON
============================== */

.login-button {

    width: 100%;

    height: 52px;

    border: none;

    border-radius: 11px;

    background: #c79645;

    color: #292824;

    font-family: inherit;

    font-size: 12px;

    font-weight: 700;

    cursor: pointer;

    margin-top: 7px;

    box-shadow:
        0 9px 18px rgba(151,111,48,0.20);

    transition: 0.2s;

}


.login-button:hover {

    background: #d5aa5e;

    transform: translateY(-2px);

}


.login-button:active {

    transform: translateY(0);

}


/* ==============================
   BOTTOM INFO
============================== */

.form-bottom {

    margin-top: 25px;

    padding-top: 18px;

    border-top:
        1px solid #ebe6dd;

    display: flex;

    align-items: center;

    justify-content: space-between;

}


.secure {

    color: #a19b92;

    font-size: 9px;

}


.system {

    color: #a77b35;

    font-size: 9px;

    font-weight: 700;

}


/* ==============================
   RESPONSIVE
============================== */

@media (max-width: 850px) {

    body {

        overflow: auto;

        padding: 15px;

    }


    .login-wrapper {

        grid-template-columns: 1fr;

    }


    .visual-panel {

        min-height: 430px;

    }


    .visual-text {

        margin-top: 65px;

    }


    .form-panel {

        min-height: 500px;

    }

}


@media (max-width: 550px) {

    .visual-panel {

        padding: 32px;

    }


    .form-panel {

        padding: 35px 25px;

    }


    .visual-text h2 {

        font-size: 32px;

    }


    .flower {

        right: 20px;

        bottom: 30px;

        font-size: 60px;

    }


    .leaf {

        right: 90px;

        bottom: 75px;

    }


    .form-bottom {

        flex-direction: column;

        align-items: flex-start;

        gap: 8px;

    }

}

</style>

</head>


<body>


<!-- BACKGROUND -->

<div class="bg-circle-one"></div>

<div class="bg-circle-two"></div>

<div class="bg-line"></div>


<!-- LOGIN -->

<div class="login-wrapper">


    <!-- =================================
         VISUAL SIDE
    ================================== -->

    <section class="visual-panel">


        <div class="brand">

            <div class="logo">
                ◇
            </div>

            <small>
                PRODUCT MANAGEMENT
            </small>

            <h1>
                Product<br>
                Information System
            </h1>

        </div>


        <div class="visual-text">

            <span>
                WELCOME TO YOUR WORKSPACE
            </span>

            <h2>
                Kelola data.<br>
                <em>Lebih terarah.</em>
            </h2>

            <p>

                Pantau produk, stok, dan
                informasi persediaan melalui
                sistem yang sederhana,
                rapi, dan mudah digunakan.

            </p>

        </div>


        <div class="flower">
            🌸
        </div>


        <div class="leaf">
            🌿
        </div>


        <div class="visual-footer">

            Product Information System © 2026

        </div>


    </section>


    <!-- =================================
         FORM SIDE
    ================================== -->

    <section class="form-panel">


        <div class="form-box">


            <div class="form-mark"></div>


            <div class="label">
                ADMIN LOGIN
            </div>


            <h2>
                Masuk
            </h2>


            <p class="subtitle">

                Masukkan akun administrator
                untuk melanjutkan ke dashboard.

            </p>


            <?php if ($error): ?>

                <div class="error">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form method="POST">


                <!-- USERNAME -->

                <div class="form-group">

                    <label for="username">
                        USERNAME
                    </label>


                    <div class="input-box">

                        <span>
                            ◉
                        </span>


                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Masukkan username"
                            autocomplete="username"
                            required
                        >

                    </div>

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        PASSWORD
                    </label>


                    <div class="input-box">

                        <span>
                            ◈
                        </span>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >


                        <button
                            type="button"
                            class="password-button"
                            onclick="togglePassword()"
                            id="passwordButton"
                        >
                            ◉
                        </button>

                    </div>

                </div>


                <button
                    type="submit"
                    class="login-button"
                >

                    Masuk ke Dashboard
                    →

                </button>


            </form>


            <div class="form-bottom">

                <div class="secure">

                    🔒 Akses administrator

                </div>


                <div class="system">

                    PIS • 2026

                </div>

            </div>


        </div>


    </section>


</div>


<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    const button =
        document.getElementById("passwordButton");


    if (password.type === "password") {

        password.type = "text";

        button.textContent = "◌";

    } else {

        password.type = "password";

        button.textContent = "◉";

    }

}

</script>


</body>

</html>