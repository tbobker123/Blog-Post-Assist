<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="mainNav">
    <div class="container px-4">
        <a class="navbar-brand" href="#page-top">Blog Post Assist</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><span class="text-white nav-link">Welcome, <?=$username??'';?></span></li>
                <?php if(isset($login_register) AND $login_register === true) : ?>
                    <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="/register">Register</a></li>
                <?php else : ?>
                    <li class="nav-item"><a class="nav-link" href="/report">Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="/configuration">Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
