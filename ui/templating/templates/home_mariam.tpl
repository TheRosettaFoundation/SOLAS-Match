{include file="new_header.tpl" body_id="home"}
<!-- Editor Hint: ¿áéíóú -->

            <div class="container">

<span class="d-none">
    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
</span>

{if isset($flash['error'])}
    <div class="alert alert-danger alert-dismissible fade show mt-4">
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}
{if isset($flash['info'])}
    <div class="alert alert-info alert-dismissible fade show mt-4">
        <p><strong>{Localisation::getTranslation('common_note')} </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}
{if isset($flash['success'])}
    <div class="alert alert-success alert-dismissible fade show mt-4 ">
        <img src="{urlFor name='home'}ui/img/success.svg" alt="translator" class="mx-1 " />
        <strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}
{if isset($flash['warning'])}
    <div class="alert alert-warning alert-dismissible fade show mt-4">
        <p><strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['warning'])}</strong></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}

                <div id="announcement-banner" class="alert alert-warning text-center p-3 d-flex align-items-center justify-content-center" role="alert">
                    <p class="mb-0 flex-grow-1">
                        System Notice: The task assignment queue is experiencing minor delays. Thank you for your patience.
                    </p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="document.getElementById('announcement-banner').style.display = 'none';"></button>
                </div>
            </div>

            <div class="container-xxl px-4 px-sm-5 px-lg-5 pb-5 pt-4">
                <div class="mb-4"><img src="https://twbplatform.org/ui/img/voice.png" alt="Voice image"></div>

                <div class="row g-4">
                    <div class="col-lg-8 order-1 order-lg-1 space-y-8">

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <div class="card custom-card p-3 h-100 card-border-top-blue">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 p-3 rounded-circle bg-primary-subtle" style="color: var(--core-blue);">
                                            <i class="fa-solid fa-clock fa-xl"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-secondary fw-medium mb-0">Hours Contributed</p>
                                            <p class="fs-4 fw-bold text-dark mb-0">145</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card custom-card p-3 h-100 card-border-top-accent">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 p-3 rounded-circle" style="background-color: #fef3c7; color: var(--twb-accent);">
                                            <i class="fa-solid fa-file-lines fa-xl"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-secondary fw-medium mb-0">Words Translated</p>
                                            <p class="fs-4 fw-bold text-dark mb-0">45,892</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card custom-card p-3 h-100 card-border-top-green">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 p-3 rounded-circle bg-success-subtle text-success">
                                            <i class="fa-solid fa-check-circle fa-xl"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-secondary fw-medium mb-0">Projects Completed</p>
                                            <p class="fs-4 fw-bold text-dark mb-0">18</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light custom-card p-4 border-top-0 card-border-top-accent">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h2 class="fs-3 fw-bold text-dark mb-0">My Tasks</h2>
                                <a href="{urlFor name="claimed-tasks" options="user_id.{$user_id}"}" class="text-decoration-none fw-semibold d-flex align-items-center" style="color: var(--twb-accent);">
                                    Go to My Tasks <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                            <div class="space-y-4">
                                {foreach from=$claimed_tasks item=task}
{assign var="task_id" value=$task->getId()}
{assign var="type_id" value=$task->getTaskType()}
{assign var="status_id" value=$task->getTaskStatus()}
{assign var="task_title" value=mb_substr($task->getTitle(), 0, 50)}
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 shadow-sm border bg-white hover-shadow">
                                    <div class="d-flex align-items-center">
                                        <div class="p-2 rounded-circle me-3" style="background-color: var(--twb-accent); opacity: 0.1; color: var(--twb-accent);">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-md">
                                                <a id="task-{$task_id}" href="{$siteLocation}task/{$task_id}/view" class="custom-link text-wrap">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task_title)}</a>
                                                <span class="badge rounded-pill text-uppercase fs-7 fw-bold" style="background-color:{TaskTypeEnum::$enum_to_UI[$type_id]['colour']}">{TaskTypeEnum::$enum_to_UI[$type_id]['type_text']}</span>
                                            </div>
                                            <p class="text-muted small mb-0">English → French | In Progress</p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <p class="small fw-medium text-danger d-flex align-items-center mb-0">
                                            <i class="fa-regular fa-clock me-1"></i> Today, 11:00 AM
<div class="process_deadline_utc_if_possible d-flex mb-3 flex-wrap align-items-center text-muted" style="visibility: hidden"> {$deadline_timestamps[$task_id]}</div>
                                        </p>
                                    </div>
                                </div>
                                {/foreach}
                            </div>
                        </div>

                        <div class="card bg-light custom-card p-4 border-top-0" style="border-top-color: var(--core-blue);">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h2 class="fs-3 fw-bold text-dark mb-0">Available Tasks</h2>
                                <a href="#browse-tasks" class="text-decoration-none fw-semibold d-flex align-items-center" style="color: var(--core-blue);">
                                    Browse All Tasks <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                            <div class="space-y-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card custom-card p-3 h-100 border-start border-4" style="border-left-color: var(--twb-accent);">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="mb-2">
                                                        <span class="badge rounded-pill text-uppercase task-type-badge fs-7 fw-bold">Translation</span>
                                                        <span class="badge rounded-pill ms-1 task-unit-badge fs-7 fw-bold">8343 words</span>
                                                    </div>
                                                    <h5 class="fw-bold text-dark mb-2">1.09 INEE Minimum Standards 2024 [FINAL DRAFT]_for translation.docx</h5>
                                                    <a id="task-286410" href="https://twbplatform.org/task/286410/view" class="custom-link text-wrap d-none">Task Link</a>
                                                </div>
                                                <img src="https://placehold.co/40x40/ED1C24/ffffff?text=IFRC" alt="IFRC logo" class="rounded-circle ms-3" width="40" height="40" />
                                            </div>
                                            <p class="small text-secondary mb-2">
                                                <span class="fw-medium">Languages:</span> English → French
                                            </p>
                                            <p class="small fw-medium text-danger d-flex align-items-center mb-3">
                                                <i class="fa-regular fa-clock me-1"></i> Today, 5:20 PM
                                            </p>
                                            <a class="btn btn-secondary fs-5 px-3" href="https://twbplatform.org/task/286410/view">View Task</a>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card custom-card p-3 h-100 border-start border-4" style="border-left-color: var(--twb-accent);">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="mb-2">
                                                        <span class="badge rounded-pill text-uppercase task-type-badge fs-7 fw-bold">Translation</span>
                                                        <span class="badge rounded-pill ms-1 task-unit-badge fs-7 fw-bold">8343 words</span>
                                                    </div>
                                                    <h5 class="fw-bold text-dark mb-2">Translations in DTP of epidural risk infographics</h5>
                                                </div>
                                                <img src="https://placehold.co/40x40/003C71/ffffff?text=OXF" alt="Oxfam logo" class="rounded-circle ms-3" width="40" height="40" />
                                            </div>
                                            <p class="small text-secondary mb-2">
                                                <span class="fw-medium">Languages:</span> Spanish → Italian
                                            </p>
                                            <p class="small fw-medium text-danger d-flex align-items-center mb-3">
                                                <i class="fa-regular fa-clock me-1"></i> 20/25/2025 10:40 PM
                                            </p>
                                            <a class="btn btn-secondary fs-5 px-3" href="https://twbplatform.org/task/286410/view">View Task</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card custom-card p-3 h-100 border-start border-4" style="border-left-color: var(--twb-accent);">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="mb-2">
                                                        <span class="badge rounded-pill text-uppercase task-type-badge fs-7 fw-bold">Translation</span>
                                                        <span class="badge rounded-pill ms-1 task-unit-badge fs-7 fw-bold">8343 words</span>
                                                    </div>
                                                    <h5 class="fw-bold text-dark mb-2">INEE - Minimum Standards 2025</h5>
                                                </div>
                                                <img src="https://placehold.co/40x40/ED1C24/ffffff?text=IFRC" alt="IFRC logo" class="rounded-circle ms-3" width="40" height="40" />
                                            </div>
                                            <p class="small text-secondary mb-2">
                                                <span class="fw-medium">Languages:</span> English → French
                                            </p>
                                            <p class="small fw-medium text-danger d-flex align-items-center mb-3">
                                                <i class="fa-regular fa-clock me-1"></i> Tomorrow, 9:30 PM
                                            </p>
                                            <a class="btn btn-secondary fs-5 px-3" href="https://twbplatform.org/task/286410/view">View Task</a>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card custom-card p-3 h-100 border-start border-4" style="border-left-color: var(--twb-accent);">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <div class="mb-2">
                                                        <span class="badge rounded-pill text-uppercase task-type-badge fs-7 fw-bold">Translation</span>
                                                        <span class="badge rounded-pill ms-1 task-unit-badge fs-7 fw-bold">8343 words</span>
                                                    </div>
                                                    <h5 class="fw-bold text-dark mb-2">Global MEL Platform - 1</h5>
                                                </div>
                                                <img src="https://placehold.co/40x40/003C71/ffffff?text=OXF" alt="Oxfam logo" class="rounded-circle ms-3" width="40" height="40" />
                                            </div>
                                            <p class="small text-secondary mb-2">
                                                <span class="fw-medium">Languages:</span> Spanish → Italian
                                            </p>
                                            <a class="btn btn-secondary fs-5 px-3 mt-3" href="https://twbplatform.org/task/286410/view">View Task</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <a class="btn btn-secondary fs-5 px-3" href="https://twbplatform.org/task/286410/view">View More Available Tasks</a>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4 order-2 order-lg-2 space-y-8">

                        <div class="card bg-light custom-card p-4">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                <span class="me-2 fs-5" style="color: var(--twb-accent);">📰</span>
                                <a href="archive.html" class="fs-5 fw-bold text-dark text-decoration-none hover-text-secondary">
                                    News & Updates
                                </a>
                            </div>
                            <ul class="list-unstyled space-y-4">
                                <li class="border-bottom pb-3">
                                    <a href="article.html" class="d-block text-decoration-none text-dark hover-bg-light p-1 rounded transition-colors">
                                        <div class="d-flex align-items-start">
                                            <span class="me-2 mt-1 flex-shrink-0" style="color: var(--twb-accent);">💡</span>
                                            <div>
                                                <p class="fw-medium text-dark mb-0">Webinar: CAT Tools for Reviewers</p>
                                                <p class="small text-secondary mb-0">Webinar • Oct 28, 2025</p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                </ul>
                            <a href="#news-archive" class="mt-3 w-100 btn btn-outline-primary fw-semibold" style="color: var(--core-blue);">
                                View All News <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>

                        <div class="card bg-light custom-card p-4">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                <span class="me-2 fs-5" style="color: var(--core-blue);">📖</span>
                                <h3 class="fs-5 fw-bold text-dark mb-0">Resources & Tools</h3>
                            </div>
                            <ul class="list-unstyled space-y-4">
                                <li>
                                    <a href="#guidelines" class="d-flex align-items-center small text-dark fw-medium text-decoration-none hover-text-primary">
                                        <span class="me-2" style="color: var(--core-blue); opacity: 0.6;">&rarr;</span> Community Contribution Guidelines (Bookstack)
                                    </a>
                                </li>
                                </ul>
                        </div>

                        <div class="card bg-light custom-card p-4">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                                <div class="p-2 rounded-circle me-3" style="background-color: var(--core-blue); opacity: 0.1;">
                                    💬
                               </div>
                               <h3 class="fs-5 fw-bold text-dark mb-0">Community Survey</h3>
                            </div>
                            <p class="fw-semibold text-dark mb-2">How satisfied are you with the platform's task matching feature?</p>
                            <p class="small text-secondary mb-3">Your anonymous feedback helps us improve task assignments and efficiency.</p>
                            <form onsubmit="alert('Response submitted!'); return false;" class="space-y-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="radio-v-satisfied" name="survey-response" value="Very Satisfied" style="color: var(--core-blue);" required>
                                    <label class="form-check-label small fw-medium text-dark w-100 p-2 rounded hover-bg-light" for="radio-v-satisfied">Very Satisfied</label>
                                </div>
                                <button type="submit" class="btn w-100 py-2 text-white fw-semibold rounded-pill mt-3 hover-opacity-90" style="background-color: var(--twb-accent);">
                                    Submit Response
                                </button>
                            </form>
                        </div>

                        <div class="card bg-light custom-card p-4 border-top-0" style="border-top: 4px solid #adb5bd;">
                            <h3 class="fs-5 fw-bold text-dark mb-2">Feedback & Suggestions</h3>
                            <p class="text-secondary mb-3 small">Have ideas for improvement? Share your experience with the platform or suggest a new feature.</p>
                            <a href="#feedback-form" class="btn rounded-pill text-white fw-medium transition-colors" style="background-color: var(--twb-accent);">
                                💡 Submit Feedback
                            </a>
                        </div>
                    </div>
                </div>
            </div>

{include file="footer2.tpl"}
