<!--=============== HEADER ===============-->
<header class="header" id="header">
    <div class="header__container">
        <a href="{{ route('admin.dashboard') }}" class="header__logo">
            <!-- <i class="ri-cloud-fill"></i> -->
            <span>Hungry FalCONs</span>
        </a>

        <button class="header__toggle" id="header-toggle">
            <i class="ri-menu-line"></i>
        </button>
    </div>
</header>

<!--=============== SIDEBAR ===============-->
@php
$userId = session()->get('loginId');
$user = App\Models\UserProfile::where('id', $userId)->first();
$userType = App\Models\UserType::where('id', $user->user_type_id)->first()->type_name;
@endphp
<!--=============== SIDEBAR ===============-->
<nav class="sidebar" id="sidebar">
    <div class="sidebar__container">
        <div class="sidebar__user">
            <!-- <div class="sidebar__img">
                <img src="assets/img/perfil.png" alt="image">
            </div> -->

            <div class="sidebar__info">
                <h3>{{ $user->first_name . ' ' . $user->last_name }}</h3>
                <span>{{ $userType }}</span>
            </div>
        </div>

        <div class="sidebar__content">
            <div>
                <h3 class="sidebar__title">MAIN</h3>

                <div class="sidebar__list">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar__link {{ Route::currentRouteName() == 'admin.dashboard' ? 'active-link' : '' }}">
                        <i class="ri-pie-chart-2-fill"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.audit.logs') }}" class="sidebar__link {{ Route::currentRouteName() == 'admin.audit.logs' ? 'active-link' : '' }}">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        <span>Audit Trail</span>
                    </a>

                    <a href="{{ route('manage.building') }}" class="sidebar__link {{ Route::currentRouteName() == 'manage.building' ? 'active-link' : '' }}">
                        <i class="bi bi-pin-map-fill"></i>
                        <span>Canteen Control</span>
                    </a>

                    <a href="{{ route('manager.account') }}" class="sidebar__link {{ Route::currentRouteName() == 'manager.account' ? 'active-link' : '' }}">
                        <i class="bi bi-person-badge-fill"></i>
                        <span>Manager's Account</span>
                    </a>

                    <a href="{{ route('buyers.account') }}" class="sidebar__link {{ Route::currentRouteName() == 'buyers.account' ? 'active-link' : '' }}">
                        <i class="bi bi-person-check"></i>
                        <span>Buyer's Account</span>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="sidebar__title">PROFILES</h3>

                <div class="sidebar__list">
                    <a href="{{ route('admin.my.profile') }}" class="sidebar__link {{ Route::currentRouteName() == 'admin.my.profile' ? 'active-link' : '' }}">
                        <i class="bi bi-person-fill-gear"></i>
                        <span>My Profile</span>
                    </a>

                    <a href="{{ route('admin.change.password') }}" class="sidebar__link {{ Route::currentRouteName() == 'admin.change.password' ? 'active-link' : '' }}">
                        <i class="ri-mail-unread-fill"></i>
                        <span>Change Password</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="sidebar__actions">
            <!-- <button>
                <i class="ri-moon-clear-fill sidebar__link sidebar__theme" id="theme-button">
                    <span>Theme</span>
                </i>
            </button> -->

            <a href="{{ route('user.logout') }}" class="sidebar__link">
                <i class="ri-logout-box-r-fill"></i>
                <span>Log Out</span>
            </a>
        </div>
    </div>
</nav>


<!--=============== MAIN ===============-->
<main class="main container" id="main">
    @yield('content')
</main>

<!--=============== MAIN JS ===============-->
<script>
    /*=============== SHOW SIDEBAR ===============*/
    const showSidebar = (toggleId, sidebarId, headerId, mainId) => {
        const toggle = document.getElementById(toggleId),
            sidebar = document.getElementById(sidebarId),
            header = document.getElementById(headerId),
            main = document.getElementById(mainId)

        if (toggle && sidebar && header && main) {
            toggle.addEventListener('click', () => {
                /* Show sidebar */
                sidebar.classList.toggle('show-sidebar')
                /* Add padding header */
                header.classList.toggle('left-pd')
                /* Add padding main */
                main.classList.toggle('left-pd')
            })
        }
    }
    showSidebar('header-toggle', 'sidebar', 'header', 'main')

    /*=============== LINK ACTIVE ===============*/
    const sidebarLink = document.querySelectorAll('.sidebar__list a')

    function linkColor() {
        sidebarLink.forEach(l => l.classList.remove('active-link'))
        this.classList.add('active-link')
    }

    sidebarLink.forEach(l => l.addEventListener('click', linkColor))

    /*=============== DARK LIGHT THEME ===============*/
    const themeButton = document.getElementById('theme-button')
    const darkTheme = 'dark-theme'
    const iconTheme = 'ri-sun-fill'

    // Previously selected topic (if user selected)
    const selectedTheme = localStorage.getItem('selected-theme')
    const selectedIcon = localStorage.getItem('selected-icon')

    // We obtain the current theme that the interface has by validating the dark-theme class
    const getCurrentTheme = () => document.body.classList.contains(darkTheme) ? 'dark' : 'light'
    const getCurrentIcon = () => themeButton.classList.contains(iconTheme) ? 'ri-moon-clear-fill' : 'ri-sun-fill'

    // We validate if the user previously chose a topic
    if (selectedTheme) {
        // If the validation is fulfilled, we ask what the issue was to know if we activated or deactivated the dark
        document.body.classList[selectedTheme === 'dark' ? 'add' : 'remove'](darkTheme)
        themeButton.classList[selectedIcon === 'ri-moon-clear-fill' ? 'add' : 'remove'](iconTheme)
    }

    // Activate / deactivate the theme manually with the button
    themeButton.addEventListener('click', () => {
        // Add or remove the dark / icon theme
        document.body.classList.toggle(darkTheme)
        themeButton.classList.toggle(iconTheme)
        // We save the theme and the current icon that the user chose
        localStorage.setItem('selected-theme', getCurrentTheme())
        localStorage.setItem('selected-icon', getCurrentIcon())
    })
</script>

<style>
    /*=============== GOOGLE FONTS ===============*/
    @import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,200..1000&display=swap");

    /*=============== VARIABLES CSS ===============*/
    :root {
        --header-height: 3.5rem;

        /*========== Colors ==========*/
        /*Color mode HSL(hue, saturation, lightness)*/
        --first-color: hsl(228, 85%, 63%);
        --title-color: hsl(228, 18%, 16%);
        --text-color: hsl(228, 8%, 56%);
        --body-color: hsl(228, 100%, 99%);
        --shadow-color: hsla(228, 80%, 4%, .1);

        /*========== Font and typography ==========*/
        /*.5rem = 8px | 1rem = 16px ...*/
        --body-font: "Nunito Sans", system-ui;
        --normal-font-size: .938rem;
        --smaller-font-size: .75rem;
        --tiny-font-size: .75rem;

        /*========== Font weight ==========*/
        --font-regular: 400;
        --font-semi-bold: 600;

        /*========== z index ==========*/
        --z-tooltip: 10;
        --z-fixed: 100;
    }

    /*========== Responsive typography ==========*/
    @media screen and (min-width: 1150px) {
        :root {
            --normal-font-size: 1rem;
            --smaller-font-size: .813rem;
        }
    }

    /*=============== BASE ===============*/
    /* * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
    } */

    body {
        font-family: var(--body-font);
        font-size: var(--normal-font-size);
        background-color: var(--body-color);
        color: var(--text-color);
        transition: background-color .4s;
    }

    a {
        text-decoration: none;
    }

    img {
        display: block;
        max-width: 100%;
        height: auto;
    }

    button {
        all: unset;
    }

    /*=============== VARIABLES DARK THEME ===============*/
    body.dark-theme {
        --first-color: hsl(228, 70%, 63%);
        --title-color: hsl(228, 18%, 96%);
        --text-color: hsl(228, 12%, 61%);
        --body-color: hsl(228, 24%, 16%);
        --shadow-color: hsla(228, 80%, 4%, .3);
    }

    /*========== 
    Color changes in some parts of 
    the website, in dark theme
    ==========*/
    .dark-theme .sidebar__content::-webkit-scrollbar {
        background-color: hsl(228, 16%, 30%);
    }

    .dark-theme .sidebar__content::-webkit-scrollbar-thumb {
        background-color: hsl(228, 16%, 40%);
    }

    /*=============== REUSABLE CSS CLASSES ===============*/
    /* .container {
        margin-inline: 1.5rem;
    } */

    /* .main {
        padding-top: 5rem;
    } */

    /*=============== HEADER ===============*/
    .header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: var(--z-fixed);
        margin: .75rem;
    }

    .header__container {
        width: 100%;
        height: var(--header-height);
        background-color: var(--body-color);
        box-shadow: 0 2px 24px var(--shadow-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-inline: 1.5rem;
        border-radius: 1rem;
        transition: background-color .4s;
    }

    .header__logo {
        display: inline-flex;
        align-items: center;
        column-gap: .25rem;
    }

    .header__logo i {
        font-size: 1.5rem;
        color: var(--first-color);
    }

    .header__logo span {
        color: var(--title-color);
        font-weight: var(--font-semi-bold);
    }

    .header__toggle {
        font-size: 1.5rem;
        color: var(--title-color);
        cursor: pointer;
    }

    /*=============== SIDEBAR ===============*/
    .sidebar {
        position: fixed;
        left: -120%;
        top: 0;
        bottom: 0;
        z-index: var(--z-fixed);
        width: 288px;
        background-color: var(--body-color);
        box-shadow: 2px 0 24px var(--shadow-color);
        padding-block: 1.5rem;
        margin: .75rem;
        border-radius: 1rem;
        transition: left .4s, background-color .4s, width .4s;
    }

    .sidebar__container,
    .sidebar__content {
        display: flex;
        flex-direction: column;
        row-gap: 3rem;
    }

    .sidebar__container {
        height: 100%;
        overflow: hidden;
    }

    .sidebar__user {
        display: grid;
        grid-template-columns: repeat(2, max-content);
        align-items: center;
        column-gap: 1rem;
        padding-left: 2rem;
    }

    .sidebar__img {
        position: relative;
        width: 50px;
        height: 50px;
        background-color: var(--first-color);
        border-radius: 50%;
        overflow: hidden;
        display: grid;
        justify-items: center;
    }

    .sidebar__img img {
        position: absolute;
        width: 36px;
        bottom: -1px;
    }

    .sidebar__info h3 {
        font-size: var(--normal-font-size);
        color: var(--title-color);
        transition: color .4s;
    }

    .sidebar__info span {
        font-size: var(--smaller-font-size);
    }

    .sidebar__content {
        overflow: hidden auto;
    }

    .sidebar__content::-webkit-scrollbar {
        width: .4rem;
        background-color: hsl(228, 8%, 85%);
    }

    .sidebar__content::-webkit-scrollbar-thumb {
        background-color: hsl(228, 8%, 75%);
    }

    .sidebar__title {
        width: max-content;
        font-size: var(--tiny-font-size);
        font-weight: var(--font-semi-bold);
        padding-left: 2rem;
        margin-bottom: 1.5rem;
    }

    .sidebar__list,
    .sidebar__actions {
        display: grid;
        row-gap: 1.5rem;
    }

    .sidebar__link {
        position: relative;
        display: grid;
        grid-template-columns: repeat(2, max-content);
        align-items: center;
        column-gap: 1rem;
        color: var(--text-color);
        padding-left: 2rem;
        transition: color .4s, opacity .4s;
    }

    .sidebar__link i {
        font-size: 1.25rem;
    }

    .sidebar__link span {
        font-weight: var(--font-semi-bold);
    }

    .sidebar__link:hover {
        color: var(--first-color);
    }

    .sidebar__actions {
        margin-top: auto;
    }

    .sidebar__actions button {
        cursor: pointer;
    }

    .sidebar__theme {
        width: 100%;
        font-size: 1.25rem;
    }

    .sidebar__theme span {
        font-size: var(--normal-font-size);
        font-family: var(--body-font);
    }

    /* Show sidebar */
    .show-sidebar {
        left: 0;
    }

    /* Active link */
    .active-link {
        color: var(--first-color);
    }

    .active-link::after {
        content: "";
        position: absolute;
        left: 0;
        width: 3px;
        height: 20px;
        background-color: var(--first-color);
    }

    /*=============== BREAKPOINTS ===============*/
    /* For small devices */
    @media screen and (max-width: 360px) {
        .header__container {
            padding-inline: 1rem;
        }

        .sidebar {
            width: max-content;
        }

        .sidebar__info,
        .sidebar__link span {
            display: none;
        }

        .sidebar__user,
        .sidebar__list,
        .sidebar__actions {
            justify-content: center;
        }

        .sidebar__user,
        .sidebar__link {
            grid-template-columns: max-content;
        }

        .sidebar__user {
            padding: 0;
        }

        .sidebar__link {
            padding-inline: 2rem;
        }

        .sidebar__title {
            padding-inline: .5rem;
            margin-inline: auto;
        }
    }

    /* For large devices */
    @media screen and (min-width: 1150px) {
        .header {
            margin: 1rem;
            padding-left: 340px;
            transition: padding .4s;
        }

        .header__container {
            height: calc(var(--header-height) + 2rem);
            padding-inline: 2rem;
        }

        .header__logo {
            order: 1;
        }

        .sidebar {
            left: 0;
            width: 316px;
            margin: 1rem;
        }

        .sidebar__info,
        .sidebar__link span {
            transition: opacity .4s;
        }

        .sidebar__user,
        .sidebar__title {
            transition: padding .4s;
        }

        /* Reduce sidebar */
        .show-sidebar {
            width: 90px;
        }

        .show-sidebar .sidebar__user {
            padding-left: 1.25rem;
        }

        .show-sidebar .sidebar__title {
            padding-left: 0;
            margin-inline: auto;
        }

        .show-sidebar .sidebar__info,
        .show-sidebar .sidebar__link span {
            opacity: 0;
        }

        .main {
            padding-left: 340px;
            padding-top: 8rem;
            transition: padding .4s;
        }

        /* Add padding left */
        .left-pd {
            padding-left: 114px;
        }
    }
</style>