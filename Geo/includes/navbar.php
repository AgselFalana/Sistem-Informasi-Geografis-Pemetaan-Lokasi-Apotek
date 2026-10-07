<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_admin = isset($_SESSION['admin_logged_in']);

$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>

.navbar {
    background: #172033;
    padding: 0;
    box-shadow: 0 2px 12px rgba(15, 23, 42, 0.18);
    border-bottom: 1px solid #27344a;

    position: sticky;
    top: 0;
    z-index: 1000;

    width: 100%;
}

.navbar-content {
    display: grid;

    grid-template-columns: 1fr auto 1fr;

    align-items: center;

    width: 100%;
    min-height: 64px;

    margin: 0;

    position: relative;
}


/* ============================================
   JUDUL
============================================ */

.navbar-title {
    color: #f8fafc;

    font-size: 16px;
    font-weight: 600;

    white-space: nowrap;

    line-height: 1.25;

    justify-self: start;

    margin-left: 60px;

    padding: 0;

    letter-spacing: -0.01em;
}


/* ============================================
   MENU
============================================ */

.navbar-menu {
    display: flex;

    align-items: center;
    justify-content: center;

    gap: 30px;

    margin: 0;
    padding: 0;

    list-style: none;

    justify-self: center;
}

.navbar-menu li {
    list-style: none;
}

.navbar-menu a {
    position: relative;

    display: flex;
    align-items: center;

    height: 64px;

    color: #aeb9ca;

    text-decoration: none;

    font-size: 13px;
    font-weight: 600;

    transition:
        color 0.2s ease,
        background-color 0.2s ease;
}

.navbar-menu a:hover {
    color: #ffffff;
}

.navbar-menu a.active {
    color: #ffffff;
}

.navbar-menu a.active::after {
    content: "";

    position: absolute;

    left: 0;
    right: 0;
    bottom: 0;

    height: 2px;

    background: #7c9cff;

    border-radius: 2px 2px 0 0;
}


/* ============================================
   BAGIAN KANAN
============================================ */

.navbar-right {
    display: flex;

    align-items: center;
    justify-content: flex-end;

    gap: 14px;

    justify-self: end;

    margin-right: 60px;
}


/* ============================================
   LOGIN DESKTOP
============================================ */

.btn-admin {
    padding: 0;

    background: transparent;

    color: #b9c6da;

    border: none;

    cursor: pointer;

    font-family: inherit;

    font-size: 13px;
    font-weight: 600;

    white-space: nowrap;

    transition: color 0.2s ease;
}

.btn-admin:hover {
    color: #ffffff;
}


/* ============================================
   LOGOUT DESKTOP
============================================ */

.btn-logout {
    display: inline-flex;

    align-items: center;

    padding: 0;

    background: transparent;

    color: #b9c6da;

    text-decoration: none;

    border: none;

    font-size: 13px;
    font-weight: 600;

    white-space: nowrap;

    transition: color 0.2s ease;
}

.btn-logout:hover {
    color: #ff9b9b;
}


/* ============================================
   HAMBURGER
============================================ */

.hamburger {
    display: none;

    width: 38px;
    height: 38px;

    border: 1px solid #334155;

    background: #202c40;

    border-radius: 8px;

    cursor: pointer;

    padding: 8px;

    flex-shrink: 0;

    transition:
        background-color 0.2s ease,
        border-color 0.2s ease;
}

.hamburger:hover {
    background: #28364c;

    border-color: #475569;
}

.hamburger span {
    display: block;

    width: 100%;
    height: 2px;

    background: #dbe4f0;

    margin: 4px 0;

    border-radius: 3px;

    transition: 0.25s ease;
}


/* ============================================
   ANIMASI HAMBURGER
============================================ */

.hamburger.active span:nth-child(1) {
    transform: translateY(6px) rotate(45deg);
}

.hamburger.active span:nth-child(2) {
    opacity: 0;
}

.hamburger.active span:nth-child(3) {
    transform: translateY(-6px) rotate(-45deg);
}


/* ============================================
   LOGIN / LOGOUT DI HAMBURGER
============================================ */

.mobile-menu-action {
    display: none;
}


/* ============================================
   LOGIN MODAL
============================================ */

.login-modal-overlay {
    display: none;

    position: fixed;

    inset: 0;

    width: 100%;
    height: 100%;

    background: rgba(15, 23, 42, 0.58);

    z-index: 2000;

    align-items: center;
    justify-content: center;

    padding: 20px;

    box-sizing: border-box;

    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
}

.login-modal-overlay.active {
    display: flex;
}


/* ============================================
   LOGIN BOX
============================================ */

.login-modal-box {
    background: #f8fafc;

    padding: 28px;

    border-radius: 12px;

    border: 1px solid #dbe2ea;

    box-shadow:
        0 20px 45px rgba(15, 23, 42, 0.22);

    width: 100%;
    max-width: 380px;

    position: relative;

    color: #172033;

    box-sizing: border-box;
}


/* ============================================
   CLOSE MODAL
============================================ */

.login-modal-close {
    position: absolute;

    top: 12px;
    right: 14px;

    width: 30px;
    height: 30px;

    display: flex;

    align-items: center;
    justify-content: center;

    background: transparent;

    border: none;

    border-radius: 6px;

    font-size: 22px;

    cursor: pointer;

    color: #94a3b8;
}

.login-modal-close:hover {
    background: #e9eef5;

    color: #334155;
}


/* ============================================
   LOGIN TITLE
============================================ */

.login-modal-box h2 {
    text-align: left;

    color: #172033;

    margin: 0 0 6px;

    font-size: 20px;

    font-weight: 700;
}

.login-modal-box h2::after {
    content: "Masuk untuk mengelola data apotek.";

    display: block;

    margin-top: 6px;

    color: #64748b;

    font-size: 12px;

    font-weight: 400;
}


/* ============================================
   FORM GROUP
============================================ */

.login-modal-box .form-group {
    margin-bottom: 16px;
}

.login-modal-box .form-group:first-of-type {
    margin-top: 22px;
}

.login-modal-box label {
    display: block;

    margin-bottom: 7px;

    color: #334155;

    font-weight: 600;

    font-size: 13px;
}


/* ============================================
   INPUT
============================================ */

.login-modal-box input {
    width: 100%;

    padding: 11px 12px;

    box-sizing: border-box;

    background: #ffffff;

    border: 1px solid #cbd5e1;

    border-radius: 8px;

    font-size: 13px;

    color: #172033;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.login-modal-box input::placeholder {
    color: #94a3b8;
}

.login-modal-box input:focus {
    outline: none;

    border-color: #5b7cfa;

    box-shadow:
        0 0 0 3px rgba(91, 124, 250, 0.10);
}


/* ============================================
   LOGIN SUBMIT
============================================ */

.btn-login-submit {
    width: 100%;

    padding: 11px 12px;

    margin-top: 4px;

    background: #304a7a;

    color: #ffffff;

    border: none;

    border-radius: 8px;

    font-size: 13px;

    font-weight: 600;

    cursor: pointer;

    transition:
        background-color 0.2s ease,
        transform 0.1s ease;
}

.btn-login-submit:hover {
    background: #263e69;
}

.btn-login-submit:active {
    transform: translateY(1px);
}

.btn-login-submit:disabled {
    opacity: 0.7;

    cursor: not-allowed;
}


/* ============================================
   ERROR LOGIN
============================================ */

.login-modal-error {
    display: none;

    background: #fef2f2;

    color: #b91c1c;

    padding: 10px 12px;

    border-radius: 7px;

    margin-bottom: 15px;

    border: 1px solid #fecaca;

    font-size: 12px;

    line-height: 1.5;
}


/* ============================================
   TABLET
============================================ */

@media (max-width: 1100px) {

    .navbar-content {
        min-height: 60px;
    }

    .navbar-title {
        margin-left: 30px;

        font-size: 14px;
    }

    .navbar-menu {
        gap: 20px;
    }

    .navbar-menu a {
        font-size: 13px;
    }

    .navbar-right {
        margin-right: 30px;
    }
}


/* ============================================
   MOBILE
============================================ */

@media (max-width: 768px) {

    .navbar-content {
        display: flex;

        align-items: center;

        min-height: 60px;

        gap: 8px;

        padding: 0 14px;

        box-sizing: border-box;
    }


    /* ----------------------------------------
       JUDUL
    ---------------------------------------- */

    .navbar-title {
        margin-left: 0;

        max-width: 260px;

        font-size: 14px;

        white-space: normal;

        line-height: 1.25;
    }


    /* ----------------------------------------
       MENU HAMBURGER
    ---------------------------------------- */

    .navbar-menu {
        display: none;

        position: absolute;

        top: 60px;

        left: 12px;
        right: 12px;

        flex-direction: column;

        align-items: stretch;

        gap: 2px;

        background: #202c40;

        padding: 6px;

        border: 1px solid #334155;

        border-radius: 10px;

        box-shadow:
            0 14px 30px rgba(15, 23, 42, 0.22);

        justify-self: auto;

        box-sizing: border-box;
    }

    .navbar-menu.active {
        display: flex;
    }

    .navbar-menu li {
        width: 100%;
    }


    /* ----------------------------------------
       LINK MENU MOBILE
    ---------------------------------------- */

    .navbar-menu a {
        width: 100%;

        height: auto;

        box-sizing: border-box;

        padding: 11px 12px;

        font-size: 13px;

        color: #b9c6da;

        border-radius: 7px;
    }

    .navbar-menu a:hover {
        background: #28364c;

        color: #ffffff;
    }

    .navbar-menu a.active {
        background: #2a3951;

        color: #ffffff;
    }

    .navbar-menu a.active::after {
        display: none;
    }


    /* ----------------------------------------
       LOGIN / LOGOUT DI HAMBURGER
    ---------------------------------------- */

    .mobile-menu-action {
        display: block;

        margin-top: 4px;

        padding-top: 4px;

        border-top: 1px solid #334155;
    }

    .mobile-menu-action a {
        display: flex !important;

        align-items: center;

        width: 100%;
    }


    /* ----------------------------------------
       WARNA LOGOUT
    ---------------------------------------- */

    .mobile-logout {
        color: #ffb4b4 !important;
    }

    .mobile-logout:hover {
        color: #ffffff !important;
    }


    /* ----------------------------------------
       BAGIAN KANAN
    ---------------------------------------- */

    .navbar-right {
        margin-left: auto;

        margin-right: 0;

        justify-self: auto;
    }


    /* ----------------------------------------
       SEMBUNYIKAN VERSI DESKTOP
    ---------------------------------------- */

    .navbar-right .btn-admin,
    .navbar-right .btn-logout {
        display: none;
    }


    /* ----------------------------------------
       HAMBURGER
    ---------------------------------------- */

    .hamburger {
        display: block;
    }
}


/* ============================================
   HP KECIL
============================================ */

@media (max-width: 480px) {

    .navbar-content {
        padding: 0 12px;
    }

    .navbar-title {
        font-size: 12px;

        max-width: 210px;
    }

    .hamburger {
        width: 36px;
        height: 36px;
    }

    .login-modal-box {
        padding: 22px 20px;
    }
}


/* ============================================
   HP SANGAT KECIL
============================================ */

@media (max-width: 360px) {

    .navbar-title {
        font-size: 11px;

        max-width: 170px;
    }

    .hamburger {
        width: 34px;
        height: 34px;
    }
}

</style>


<!-- ============================================
     NAVBAR
============================================ -->

<nav class="navbar">

    <div class="navbar-content">


        <!-- ====================================
             JUDUL
        ===================================== -->

        <div class="navbar-title">
            Sistem Informasi Geografis
            <br>
            Pemetaan Lokasi Apotek
        </div>


        <!-- ====================================
             MENU
        ===================================== -->

        <ul
            class="navbar-menu"
            id="navbarMenu"
        >

            <!-- APOTEK -->

            <li>
                <a
                    href="apotek.php"
                    class="<?= $current_page == 'apotek.php' ? 'active' : '' ?>"
                >
                    Apotek
                </a>
            </li>


            <?php if ($is_admin): ?>

                <!-- ADMIN -->

                <li>
                    <a
                        href="admin.php"
                        class="<?= $current_page == 'admin.php' ? 'active' : '' ?>"
                    >
                        Admin
                    </a>
                </li>


                <!-- LOGOUT DI HAMBURGER -->

                <li class="mobile-menu-action">

                    <a
                        href="logout.php"
                        class="mobile-logout"
                    >
                        LOGOUT
                    </a>

                </li>

            <?php else: ?>

                <!-- LOGIN DI HAMBURGER -->

                <li class="mobile-menu-action">

                    <a
                        href="#"
                        onclick="openLoginModal(); return false;"
                    >
                        Login
                    </a>

                </li>

            <?php endif; ?>

        </ul>


        <!-- ====================================
             BAGIAN KANAN
        ===================================== -->

        <div class="navbar-right">

            <?php if ($is_admin): ?>

                <!-- DESKTOP LOGOUT -->

                <a
                    href="logout.php"
                    class="btn-logout"
                >
                    LOGOUT
                </a>

            <?php else: ?>

                <!-- DESKTOP LOGIN -->

                <button
                    type="button"
                    class="btn-admin"
                    onclick="openLoginModal()"
                >
                    Login
                </button>

            <?php endif; ?>


            <!-- HAMBURGER -->

            <button
                type="button"
                class="hamburger"
                id="hamburgerBtn"
                onclick="toggleNavbar()"
                aria-label="Buka menu"
                aria-expanded="false"
            >

                <span></span>
                <span></span>
                <span></span>

            </button>

        </div>

    </div>

</nav>


<!-- ============================================
     LOGIN MODAL
============================================ -->

<?php if (!$is_admin): ?>

<div
    class="login-modal-overlay"
    id="loginModalOverlay"
    onclick="if(event.target === this) closeLoginModal()"
>

    <div class="login-modal-box">


        <!-- CLOSE -->

        <button
            type="button"
            class="login-modal-close"
            onclick="closeLoginModal()"
            aria-label="Tutup"
        >
            &times;
        </button>


        <!-- TITLE -->

        <h2>
            Login Admin
        </h2>


        <!-- ERROR -->

        <div
            class="login-modal-error"
            id="loginModalError"
        >
        </div>


        <!-- FORM -->

        <form
            id="loginModalForm"
            onsubmit="return submitLoginModal(event)"
        >

            <!-- USERNAME -->

            <div class="form-group">

                <label for="modalUsername">
                    Username
                </label>

                <input
                    type="text"
                    id="modalUsername"
                    name="username"
                    required
                    autocomplete="username"
                >

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="modalPassword">
                    Password
                </label>

                <input
                    type="password"
                    id="modalPassword"
                    name="password"
                    required
                    autocomplete="current-password"
                >

            </div>


            <!-- SUBMIT -->

            <button
                type="submit"
                class="btn-login-submit"
                id="loginModalSubmitBtn"
            >
                Login
            </button>

        </form>


        <!-- DEMO -->

        <p
            style="
                text-align:center;
                margin-top:15px;
                margin-bottom:0;
                color:#94a3b8;
                font-size:11px;
            "
        >
            Demo: admin / admin123
        </p>

    </div>

</div>

<?php endif; ?>


<script>

/* ============================================
   HAMBURGER
============================================ */

function toggleNavbar() {

    const menu =
        document.getElementById('navbarMenu');

    const button =
        document.getElementById('hamburgerBtn');

    if (!menu || !button) {
        return;
    }


    menu.classList.toggle('active');

    button.classList.toggle('active');


    const isOpen =
        menu.classList.contains('active');


    button.setAttribute(
        'aria-expanded',
        isOpen ? 'true' : 'false'
    );
}


/* ============================================
   TUTUP MENU SETELAH KLIK LINK
============================================ */

document
    .querySelectorAll('.navbar-menu a')
    .forEach(function(link) {

        link.addEventListener(
            'click',
            function() {

                const menu =
                    document.getElementById(
                        'navbarMenu'
                    );

                const button =
                    document.getElementById(
                        'hamburgerBtn'
                    );


                /*
                 * Kalau link Login,
                 * jangan ganggu modal.
                 */

                if (
                    link.getAttribute('href') === '#'
                ) {

                    if (menu) {
                        menu.classList.remove(
                            'active'
                        );
                    }

                    if (button) {

                        button.classList.remove(
                            'active'
                        );

                        button.setAttribute(
                            'aria-expanded',
                            'false'
                        );
                    }

                    return;
                }


                /* Link biasa */

                if (menu) {
                    menu.classList.remove(
                        'active'
                    );
                }

                if (button) {

                    button.classList.remove(
                        'active'
                    );

                    button.setAttribute(
                        'aria-expanded',
                        'false'
                    );
                }

            }
        );

    });


/* ============================================
   KLIK DI LUAR NAVBAR
============================================ */

document.addEventListener(
    'click',
    function(event) {

        const navbar =
            document.querySelector('.navbar');

        const menu =
            document.getElementById(
                'navbarMenu'
            );

        const button =
            document.getElementById(
                'hamburgerBtn'
            );


        if (
            !navbar ||
            !menu ||
            !button
        ) {
            return;
        }


        if (!navbar.contains(event.target)) {

            menu.classList.remove(
                'active'
            );

            button.classList.remove(
                'active'
            );

            button.setAttribute(
                'aria-expanded',
                'false'
            );
        }

    }
);


/* ============================================
   LOGIN MODAL
============================================ */

function openLoginModal() {

    const overlay =
        document.getElementById(
            'loginModalOverlay'
        );

    const username =
        document.getElementById(
            'modalUsername'
        );


    if (!overlay) {
        return;
    }


    /*
     * Tutup hamburger terlebih dahulu
     */

    const menu =
        document.getElementById(
            'navbarMenu'
        );

    const button =
        document.getElementById(
            'hamburgerBtn'
        );


    if (menu) {
        menu.classList.remove(
            'active'
        );
    }

    if (button) {

        button.classList.remove(
            'active'
        );

        button.setAttribute(
            'aria-expanded',
            'false'
        );
    }


    /*
     * Buka modal
     */

    overlay.classList.add(
        'active'
    );


    /*
     * Fokus username
     */

    if (username) {

        setTimeout(
            function() {
                username.focus();
            },
            100
        );

    }
}


/* ============================================
   CLOSE LOGIN
============================================ */

function closeLoginModal() {

    const overlay =
        document.getElementById(
            'loginModalOverlay'
        );

    const errorBox =
        document.getElementById(
            'loginModalError'
        );

    const form =
        document.getElementById(
            'loginModalForm'
        );


    if (overlay) {

        overlay.classList.remove(
            'active'
        );
    }


    if (errorBox) {

        errorBox.style.display =
            'none';

        errorBox.textContent =
            '';
    }


    if (form) {
        form.reset();
    }
}


/* ============================================
   ESCAPE
============================================ */

document.addEventListener(
    'keydown',
    function(e) {

        if (e.key === 'Escape') {

            closeLoginModal();


            const menu =
                document.getElementById(
                    'navbarMenu'
                );

            const button =
                document.getElementById(
                    'hamburgerBtn'
                );


            if (menu) {

                menu.classList.remove(
                    'active'
                );
            }


            if (button) {

                button.classList.remove(
                    'active'
                );

                button.setAttribute(
                    'aria-expanded',
                    'false'
                );
            }

        }

    }
);


/* ============================================
   LOGIN AJAX
============================================ */

function submitLoginModal(e) {

    e.preventDefault();


    const btn =
        document.getElementById(
            'loginModalSubmitBtn'
        );

    const errorBox =
        document.getElementById(
            'loginModalError'
        );

    const username =
        document.getElementById(
            'modalUsername'
        ).value;

    const password =
        document.getElementById(
            'modalPassword'
        ).value;


    errorBox.style.display =
        'none';

    errorBox.textContent =
        '';


    btn.disabled =
        true;

    btn.textContent =
        'Memproses...';


    /*
     * FORM DATA
     */

    const formData =
        new URLSearchParams();


    formData.append(
        'username',
        username
    );

    formData.append(
        'password',
        password
    );


    /*
     * LOGIN AJAX
     *
     * JANGAN DIUBAH KE login.php
     */

    fetch(
        'login_ajax.php',
        {
            method: 'POST',

            headers: {
                'Content-Type':
                    'application/x-www-form-urlencoded'
            },

            body: formData
        }
    )

    .then(function(res) {

        return res.json();

    })

    .then(function(data) {

        if (data.success) {

            /*
             * Login berhasil
             */

            window.location.reload();

        } else {

            /*
             * Login gagal
             */

            errorBox.textContent =
                data.message ||
                'Login gagal';

            errorBox.style.display =
                'block';


            btn.disabled =
                false;

            btn.textContent =
                'Login';
        }

    })

    .catch(function() {

        /*
         * Error koneksi
         */

        errorBox.textContent =
            'Terjadi kesalahan koneksi. Coba lagi.';

        errorBox.style.display =
            'block';


        btn.disabled =
            false;

        btn.textContent =
            'Login';

    });


    return false;
}

</script>