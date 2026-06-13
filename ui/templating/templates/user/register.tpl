<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" data-bs-theme="light">
    <head>
        <meta charset="utf-8" content="application/xhtml+xml" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{Settings::get('site.title')}</title>
        <meta name="description" content="{Settings::get('site.meta_desc')}" />
        <meta name="keywords" content="{Settings::get('site.meta_key')}" />

        <!-- Open Graph data -->
        <meta property="og:title" content="{Settings::get('openGraph.title')}"/>
        <meta property="og:type" content="{Settings::get('openGraph.type')}" />
        <meta property="og:image" content="{Settings::get('openGraph.image')}"/>
        <meta property="og:site_name" content="{Settings::get('openGraph.site_name')}"/>
        <meta property="og:description" content="{Settings::get('openGraph.description')}"/>

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="{Settings::get('twitter.card')}"/>
        <meta name="twitter:site" content="{Settings::get('twitter.site')}"/>
        <meta name="twitter:title" content="{Settings::get('twitter.title')}"/>
        <meta name="twitter:description" content="{Settings::get('twitter.description')}"/>
        <meta name="twitter:image" content="{Settings::get('twitter.image')}"/>

        <link href="{urlFor name="home"}ui/css/custom.css" rel="stylesheet" type="text/css">
        <link rel="shortcut icon" type="image/x-icon" href="{urlFor name="home"}ui/img/favicon/faviconM.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <style>
            #registerform label         { color: #364f67 !important; }
            #registerform .check        { color: #333 !important; }
            #registerform label.error   { width: auto; }
            #registerform .error        { color: #F00 !important; }
            .required:after             { content: " *"; color: red; }
            .register_header1           { text-align: right; color: #364f67 !important; }
            .register_header2           { text-align: left;  color: #e8991c !important; }
        </style>

        {if isset($extra_styles)}{$extra_styles}{/if}
        {if isset($platformJS)}{$platformJS}{/if}

        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>

        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-3Z3VNH71D6"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', 'G-3Z3VNH71D6');
        </script>

        {if isset($extra_scripts)}{$extra_scripts}{/if}

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </head>

    <body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if} class="d-flex align-items-center min-vh-100">

        <div class="container-fluid px-4 flex-grow-1">
            <div class="row py-2">

                <!-- Form column -->
                <div class="col-12 col-md-6 py-4 d-flex flex-column align-items-center justify-content-center">
                    <div style="width: 100%; max-width: 420px;">

                        <a class="navbar-brand" href="{urlFor name='home'}">
                            <img src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="mb-4" />
                        </a>

                        {include file="handle-flash-messages.tpl"}

                        {if isset($error)}
                            <div class="alert alert-danger">
                                <strong>{Localisation::getTranslation('ff_error')}:</strong> {$error}
                            </div>
                        {/if}

                        {if isset($warning)}
                            <div class="alert">
                                <strong>{Localisation::getTranslation('ff_warning')}:</strong> {$warning}
                            </div>
                        {/if}

                        <!-- Heading + language picker -->
                        <div class="mb-4 mt-4 d-flex align-items-center gap-2">
                            <h2 class="fw-bold mb-0 flex-grow-1">{Localisation::getTranslation('ff_creat_ac')}</h2>
                            <div class="dropdown flex-shrink-0">
                                <button class="btn btngray-sm dropdown-toggle d-flex align-items-center gap-1"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-globe"></i>
                                    <span>{Localisation::getTranslation('ff_language')}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 0; width: 100%;">
                                    <li><a class="dropdown-item" href="{urlFor name="register"}?language=en">EN</a></li>
                                    <li><a class="dropdown-item" href="{urlFor name="register"}?language=fr">FR</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Google sign-in -->
                        <div id="gSignInWrapper" class="mt-4 mb-4">
                            <div id="g_id_onload"
                                data-client_id="{Settings::get('googlePlus.client_id')}"
                                data-context="signin"
                                data-ux_mode="popup"
                                data-login_uri="{Settings::get('site.location')}login/"
                                data-auto_prompt="false">
                            </div>
                            <div class="g_id_signin"
                                data-type="standard"
                                data-shape="rectangular"
                                data-theme="outline"
                                data-text="signin_with"
                                data-size="large"
                                data-width="219"
                                data-logo_alignment="left">
                            </div>
                        </div>

                        <div class="d-flex align-items-center mt-4 mb-4">
                            <div class="flex-fill border-top border-1 border-dark-subtle"></div>
                            <div class="text-center mx-4 text-muted">{Localisation::getTranslation('ff_or_continue')}</div>
                            <div class="flex-fill border-top border-1 border-dark-subtle"></div>
                        </div>

                        {if (empty($disabled))}
                            <form method="post" id="registerform" action="{urlFor name="register"}" class="wel mt-3" accept-charset="utf-8">

                                <!-- First name / Last name — stack on mobile, side by side on sm+ -->
                                <div class="row g-2 mb-2">
                                    <div class="col-12 col-sm-6">
                                        <label for="first_name" class="required mb-1"><strong>{Localisation::getTranslation('ff_first')}</strong></label>
                                        <input type="text" name="first_name" id="first_name"
                                               placeholder="{Localisation::getTranslation('ff_first')}"
                                               class="form-control"
                                               {if isset($first_name)}value="{$first_name|escape:'html':'UTF-8'}"{/if}
                                               required />
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="last_name" class="required mb-1"><strong>{Localisation::getTranslation('ff_last')}</strong></label>
                                        <input type="text" name="last_name" id="last_name"
                                               placeholder="{Localisation::getTranslation('ff_last')}"
                                               class="form-control"
                                               {if isset($last_name)}value="{$last_name|escape:'html':'UTF-8'}"{/if}
                                               required />
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label for="email" class="required form-label mb-1"><strong>Email</strong></label>
                                    <input type="text" name="email" id="email" class="form-control"
                                           placeholder="{Localisation::getTranslation('ff_your_email')}"
                                           {if isset($email)}value="{$email|escape:'html':'UTF-8'}"{/if} />
                                </div>

                                <div class="mb-2">
                                    <label for="password" class="required form-label mb-1"><strong>{Localisation::getTranslation('ff_password')}</strong></label>
                                    <input type="password" name="password" id="password" class="form-control"
                                           placeholder="{Localisation::getTranslation('ff_your_pw')}" />
                                </div>

                                <div class="mb-2">
                                    <label for="confirm_password" class="required form-label mb-1"><strong>{Localisation::getTranslation('ff_confirm_pw')}</strong></label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                           placeholder="{Localisation::getTranslation('ff_please_confirm')}" />
                                </div>

                                <div class="mb-1">
                                    <label class="checkbox required check form-check-label mt-2">
                                        <input name="age_consent" id="age_consent" class="form-check-input" type="checkbox">
                                        {Localisation::getTranslation('ff_i_confirm')}
                                    </label>
                                    <i class="fa-solid fa-circle-question ms-1" id="tool"
                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                       data-bs-title="{Localisation::getTranslation('ff_if_18')}"></i>
                                </div>

                                <div class="mb-1">
                                    <label class="checkbox check form-check-label d-flex">
                                        <input name="conduct_consent" id="conduct_consent" class="form-check-input" type="checkbox">
                                        <span class="ms-1 required">{Localisation::getTranslation('ff_i_agree')}</span>
                                    </label>
                                </div>

                                <div class="mb-1">
                                    <label class="checkbox check form-check-label d-flex">
                                        <input name="newsletter_consent" id="newsletter_consent" class="form-check-input" type="checkbox">
                                        <span class="ms-1">
                                            {Localisation::getTranslation('ff_subscribe')}
                                            <small>{Localisation::getTranslation('ff_you_can_un')}</small>
                                        </span>
                                    </label>
                                </div>

                                <input type="hidden" name="g-recaptcha-response" id="g_response">

                                <div class="d-grid gap-2 reg_btn mt-3">
                                    <button type="submit" class="btngray-lg w-100 text-center" name="submit">
                                        {Localisation::getTranslation('ff_register')}
                                    </button>
                                </div>

                                <div class="fs-5 text-muted text-center mt-2">
                                    <a href="{urlFor name="login"}" class="link-grayish link-offset-2 link-offset-3-hover link-underline-grayish link-underline-opacity-0 link-underline-opacity-75-hover">
                                        {Localisation::getTranslation('ff_i_already')}
                                    </a>
                                </div>

                            </form>
                        {/if}

                    </div>
                </div>

                <!-- Decorative image — hidden on mobile to avoid awkward stacking -->
                <div class="col-md-6 py-4 d-none d-md-flex align-items-center justify-content-center">
                    <img src="{urlFor name='home'}ui/img/login_register.svg" alt="login screen image" class="img-fluid" />
                </div>

            </div>
        </div>

    </body>
</html>
