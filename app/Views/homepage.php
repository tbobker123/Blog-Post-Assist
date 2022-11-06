
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Scrolling Nav - Start Bootstrap Template</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="homepage-css.css" rel="stylesheet" />
        <link rel="stylesheet" id="bootstrap-icons-css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css?ver=6.1" media="all">
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
            <div class="container px-4">
                <a class="navbar-brand" href="#page-top">Blog Post Assist</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><span class="text-white nav-link">Welcome, <?=$username;?></span></li>
                        <?php if($login_register === true) : ?>
                            <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                            <li class="nav-item"><a class="nav-link" href="/register">Register</a></li>
                        <?php else : ?>
                            <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Header-->
        <header class="bg-primary bg-gradient text-white">
            <div class="container px-4 text-center">
                <h1 class="fw-bolder display-2">Write Blog Posts that Rank</h1>
                <p class="lead display-5">Generate Unlimited SEO reports that analyse SERP</p>
                <?php if($login_register === true) : ?>
                            <a class="btn btn-lg btn-light" href="/register">Join Now &pound;9.99 p/m</a>
                        <?php else : ?>
                            <a class="btn btn-lg btn-light" href="/dashboard">Generate Report</a>
                        <?php endif; ?>
            </div>
        </header>
        <!-- About section-->
        <section id="about">
            <div class="container px-4">
                <div class="row gx-4 justify-content-center">
                    <div class="col-lg-8">
                        <p class="fs-3 text-center">
                            Our SEO reports <strong>analyse the top ranking pages in SERPs</strong> to help you to write SEO optimised blog posts. 
                        </p>
                        
                        <p class="fs-3 text-center">
                            <strong>Analyse top keyword phrases</strong> and post outlines are ranking high in Google. 
                        </p>

                        <p class="fs-3 text-center">
                            <strong>Generate unlimited SEO reports</strong>. With no report limit, blog post creation isn't slowed down. 
                        </p>

                        <p class="fs-3 text-center">
                            Configure your own serpapi key and OpenAI account making it more cost effective than alternatives that resell vendors services.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Services section-->
        <section class="bg-light p-5" id="services">
            <div class="container px-4">
                <div class="row gx-4 justify-content-center">
                    <div class="col-lg-12 d-flex">

                    <div class="row grid justify-content-center">
                        <div class="col-xs-12 col-md-6 col-lg-3 p-2">
                            <div class="border border-2 rounded p-2 h-100 bg-white">
                                <i class="bi bi-google display-1"></i>
                                <h2 class="fw-bold">Analyse SERP</h2>
                                <h4>Word count and headings</h4>
                                <p>
                                Quickly analyse SERP for recommended word count and blog post outline ideas.
                                </p>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-3 p-2">
                            <div class="border border-2 rounded p-2 h-100 bg-white">
                                <i class="bi bi-body-text display-1"></i>
                                <h2 class="fw-bold">Generate Content</h2>
                                <h4>Use AI to create content</h4>
                                <p>
                                Utilising OpenAI, quickly create blog post outlines and content.
                                </p>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-3 p-2">
                            <div class="border border-2 rounded p-2 h-100 bg-white">
                                <i class="bi bi-pencil-square display-1"></i>
                                <h2 class="fw-bold">Text Editor</h2>
                                <h4>Create a draft blog post</h4>
                                <p>
                                Using the built in editor, draft your blog post before pasting it into WordPress.
                                </p>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-3 p-2">
                            <div class="border border-2 rounded p-2 h-100 bg-white">
                                <i class="bi bi-question-circle-fill display-1"></i>
                                <h2 class="fw-bold">Related Questions</h2>
                                <h4>Get Related questions</h4>
                                <p>
                                SERP analysis sends back related questions for your search for more ideas.
                                </p>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </section>
        <!-- Contact section-->
        <section id="contact" class="pt-5">
            <div class="container px-4">
                <div class="row gx-4 justify-content-center">

                    <div class="col-lg-8 text-center mb-5">
                        <h2 class="text-center display-5 fw-bold">Recommended Blog Post Length</h2>
                        <img src="images/screenshot.png" alt="" class="border border-2 rounded mt-2">
                    </div>

                    <div class="col-lg-8 text-center mb-5">
                        <h2 class="text-center display-5 fw-bold">Analyse SERPs</h2>
                        <img src="images/serp.png" alt="" class="border border-2 rounded mt-2">
                    </div>

                    <div class="col-lg-8 text-center mb-5">
                        <h2 class="text-center display-5 fw-bold">Editor and Keyword Phrases</h2>
                        <img src="images/editor.png" alt="" class="border border-2 rounded mt-2">
                    </div>

                </div>
            </div>
        </section>
        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container px-4"><p class="m-0 text-center text-white">Copyright &copy; Your Website 2022</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="homepage-js.js"></script>
    </body>
</html>
