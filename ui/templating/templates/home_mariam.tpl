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

    <div id="announcement-banner" class="p-3 text-center text-sm font-medium bg-yellow-100 text-yellow-800 flex items-center justify-center space-x-3">
        <p class="flex-1">
            System Notice: The task assignment queue is experiencing minor delays. Thank you for your patience.
        </p>
        <button onclick="document.getElementById('announcement-banner').style.display = 'none';" class="p-1 rounded-full hover:bg-opacity-70 transition-opacity">
            &times;
        </button>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-8 pb-8 pt-8">
    <div><img src="https://twbplatform.org/ui/img/voice.png"></div>
    <div>&nbsp;</div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8 order-1 lg:order-1">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Hours Contributed Card -->
                <div class="bg-white rounded-xl shadow-lg p-4 border-t-4" style="border-top-color: var(--core-blue);">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full" style="color: var(--core-blue);">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Hours Contributed</p>
                            <p class="text-xl font-bold text-gray-900">145</p>
                        </div>
                    </div>
                </div>
                <!-- Words Translated Card -->
                <div class="bg-white rounded-xl shadow-lg p-4 border-t-4" style="border-top-color: var(--twb-accent);">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 p-3 rounded-full" style="color: var(--twb-accent);">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Words Translated</p>
                            <p class="text-xl font-bold text-gray-900">45,892</p>
                        </div>
                    </div>
                </div>

                <!-- Projects Completed Card -->
                <div class="bg-white rounded-xl shadow-lg p-4 border-t-4" style="border-top-color: #16a34a;">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 p-3 rounded-full text-green-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Projects Completed</p>
                            <p class="text-xl font-bold text-gray-900">18</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl shadow-lg border-t-4" style="border-top-color: var(--twb-accent);">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">My Tasks</h2>
                    <a href="#my-tasks" class="text-sm font-semibold transition-colors flex items-center" style="color: var(--twb-accent);">
                        Go to My Tasks →
                    </a>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition duration-300">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-full" style="background-color: var(--twb-accent); opacity: 0.1; color: var(--twb-accent);">
                                ✔
                            </div>
                            <div>
                                <div class="fw-bold text-md">
                                    <a id="task-286410" href="https://twbplatform.org/task/286410/view" class="custom-link w-75 text-wrap ">1.09 INEE Minimum Standards 2024</a>
                                    <span class=" badge rounded-pill border border-2 text-white text-uppercase border-greenBorder border-opacity-25 fs-7 font-bold" style="background-color:#1D8A11">  Translation </span>
                                    <span  class=" ms-1 rounded-pill badge bg-quartenary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> 8343 words </span>
                                </div>
                                <p class="text-xs text-gray-500">English → French | In Progress</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-red-500 flex items-center">
                                🕒
                                Today, 11:00 AM
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition duration-300">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-full" style="background-color: var(--twb-accent); opacity: 0.1; color: var(--twb-accent);">
                                ✔
                            </div>
                            <div>
                                <div class="fw-bold text-md">
                                    <a id="task-286410" href="https://twbplatform.org/task/286410/view" class="custom-link w-75 text-wrap ">1.03 INEE Minimum Standards Minimum 2024 [FINAL DRAFT]_for translation.docx</a>
                                    <span class=" badge rounded-pill border border-2 text-white text-uppercase border-greenBorder border-opacity-25 fs-7 font-bold" style="background-color:#1D8A11">  Translation </span>
                                    <span  class=" ms-1 rounded-pill badge bg-quartenary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> 8343 words </span>
                                </div>
                                <p class="text-xs text-gray-500">English → French | In Progress</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-red-500 flex items-center">
                                🕒
                                Today, 11:00 AM
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition duration-300">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-full" style="background-color: var(--twb-accent); opacity: 0.1; color: var(--twb-accent);">
                                ✔
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Revision</p>
                                <p class="text-xs text-gray-500">English → French | Ready to Submit</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-red-500 flex items-center">
                                🕒
                                10/25/2025 9:30 AM
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl shadow-lg border-t-4" style="border-top-color: var(--core-blue);">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Available Tasks</h2>
                    <a href="#browse-tasks" class="text-sm font-semibold transition-colors flex items-center" style="color: var(--core-blue);">
                        Browse All Tasks →
                    </a>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl shadow-lg border-l-4 flex flex-col space-y-3" style="border-left-color: var(--twb-accent);">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <div class="flex space-x-2 text-sm font-semibold text-white">
                                        <span class=" badge rounded-pill border border-2 text-white text-uppercase border-greenBorder border-opacity-25 fs-7 font-bold" style="background-color:#1D8A11">  Translation </span>
                                        <span  class=" ms-1 rounded-pill badge bg-quartenary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> 8343 words </span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-800">1.09 INEE Minimum Standards 2024 [FINAL DRAFT]_for translation.docx</p>
                                    <div class="fw-bold text-lg">
                                        <a id="task-286410" href="https://twbplatform.org/task/286410/view" class="custom-link w-75 text-wrap ">1.09 INEE Minimum Standards 2024 [FINAL DRAFT]_for translation.docx</a>
                                    </div>
                                </div>
                                <img src="https://placehold.co/40x40/ED1C24/ffffff?text=IFRC" alt="IFRC logo" class="w-10 h-10 rounded-full object-cover" />
                            </div>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Languages:</span> English → French
                            </p>
                            <p class="text-xs font-medium text-red-500 flex items-center">
                                🕒
                                Today, 5:20 PM
                            </p>
                            <div class="d-flex items-center justify-center flex-wrap mt-2 mt-sm-2 mt-md-2">
                                <a class="btn btn-secondary fs-5 px-3" href="https://twbplatform.org/task/286410/view">View Task</a>
                            </div>
                        </div>
                        <div class="p-4 rounded-xl shadow-lg border-l-4 flex flex-col space-y-3" style="border-left-color: var(--twb-accent);">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <div class="flex space-x-2 text-sm font-semibold text-white">
                                        <span class=" badge rounded-pill border border-2 text-white text-uppercase border-greenBorder border-opacity-25 fs-7 font-bold" style="background-color:#1D8A11">  Translation </span>
                                        <span  class=" ms-1 rounded-pill badge bg-quartenary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> 8343 words </span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-800">Translations in DTP of epidural risk infographics</p>
                                </div>
                                <img src="https://placehold.co/40x40/003C71/ffffff?text=OXF" alt="Oxfam logo" class="w-10 h-10 rounded-full object-cover" />
                            </div>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Languages:</span> Spanish → Italian
                            </p>
                            <p class="text-xs font-medium text-red-500 flex items-center">
                                🕒
                                20/25/2025 10:40 PM
                            </p>
                            <div class="d-flex items-center justify-center flex-wrap mt-2 mt-sm-2 mt-md-2">
                                <a class="btn btn-secondary fs-5 px-3" href="https://twbplatform.org/task/286410/view">View Task</a>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl shadow-lg border-l-4 flex flex-col space-y-3" style="border-left-color: var(--twb-accent);">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <div class="flex space-x-2 text-sm font-semibold text-white">
                                        <span class=" badge rounded-pill border border-2 text-white text-uppercase border-greenBorder border-opacity-25 fs-7 font-bold" style="background-color:#1D8A11">  Translation </span>
                                        <span  class=" ms-1 rounded-pill badge bg-quartenary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> 8343 words </span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-800">INEE - Minimum Standards 2025</p>
                                </div>
                                <img src="https://placehold.co/40x40/ED1C24/ffffff?text=IFRC" alt="IFRC logo" class="w-10 h-10 rounded-full object-cover" />
                            </div>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Languages:</span> English → French
                            </p>
                            <p class="text-xs font-medium text-red-500 flex items-center">
                                🕒
                                Tomorrow, 9:30 PM
                            </p>
                            <div class="d-flex items-center justify-center flex-wrap mt-2 mt-sm-2 mt-md-2">
                                <a class="btn btn-secondary fs-5 px-3" href="https://twbplatform.org/task/286410/view">View Task</a>
                            </div>
                        </div>
                        <div class="p-4 rounded-xl shadow-lg border-l-4 flex flex-col space-y-3" style="border-left-color: var(--twb-accent);">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <div class="flex space-x-2 text-sm font-semibold text-white">
                                        <span class=" badge rounded-pill border border-2 text-white text-uppercase border-greenBorder border-opacity-25 fs-7 font-bold" style="background-color:#1D8A11">  Translation </span>
                                        <span  class=" ms-1 rounded-pill badge bg-quartenary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> 8343 words </span>
                                    </div>
                                    <p class="text-lg font-bold text-gray-800">Global MEL Platform - 1</p>
                                </div>
                                <img src="https://placehold.co/40x40/003C71/ffffff?text=OXF" alt="Oxfam logo" class="w-10 h-10 rounded-full object-cover" />
                            </div>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Languages:</span> Spanish → Italian
                            </p>
                            <div class="d-flex items-center justify-center flex-wrap mt-2 mt-sm-2 mt-md-2">
                                <a class="btn btn-secondary fs-5 px-3" href="https://twbplatform.org/task/286410/view">View Task</a>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="text-center mt-6">
                <div class="d-flex items-center justify-center flex-wrap mt-2 mt-sm-4 mt-md-0">
                    <a class="btn btn-secondary fs-5 px-3" href="https://twbplatform.org/task/286410/view">View More Available Tasks</a>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1 space-y-8 order-2 lg:order-2">

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-1 xl:grid-cols-1 gap-8">
            <div class="bg-gray-50 p-6 rounded-xl shadow-lg">
                <div class="bg-gray-50flex items-center space-x-3 mb-4 border-b pb-3">
                    <span class="w-6 h-6" style="color: var(--twb-accent);">📰</span>
                    <a href="archive.html" class="text-xl font-bold text-gray-800 cursor-pointer hover:text-gray-600 transition-colors">
                        News & Updates
                    </a>
                </div>
                <ul class="space-y-3">
                    <li class="border-b last:border-b-0 pb-3">
                        <a href="article.html" class="block hover:bg-gray-50 -mx-1 p-1 rounded transition duration-200 cursor-pointer">
                            <div class="flex items-start space-x-3">
                                <span class="w-5 h-5 mt-1 flex-shrink-0" style="color: var(--twb-accent);">💡</span>
                                <div>
                                    <p class="font-medium text-gray-700">Webinar: CAT Tools for Reviewers</p>
                                    <p class="text-xs text-gray-500">Webinar • Oct 28, 2025</p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="border-b last:border-b-0 pb-3">
                        <a href="article.html" class="block hover:bg-gray-50 -mx-1 p-1 rounded transition duration-200 cursor-pointer">
                            <div class="flex items-start space-x-3">
                                <span class="w-5 h-5 mt-1 flex-shrink-0" style="color: var(--twb-accent);">📊</span>
                                <div>
                                    <p class="font-medium text-gray-700">TWB's 2024 Impact Report Published</p>
                                    <p class="text-xs text-gray-500">Achievement • Oct 15, 2025</p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="border-b last:border-b-0 pb-3">
                        <a href="article.html" class="block hover:bg-gray-50 -mx-1 p-1 rounded transition duration-200 cursor-pointer">
                            <div class="flex items-start space-x-3">
                                <span class="w-5 h-5 mt-1 flex-shrink-0" style="color: var(--twb-accent);">🗞️</span>
                                <div>
                                    <p class="font-medium text-gray-700">New Article: The Future of Neural MT</p>
                                    <p class="text-xs text-gray-500">Article • Oct 10, 2025</p>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                <a href="#news-archive" class="mt-4 w-full text-sm font-semibold transition-colors flex items-center justify-center py-2 rounded-lg" style="color: var(--core-blue);" onmouseover="this.style.backgroundColor = 'var(--core-blue)' + '1A';" onmouseout="this.style.backgroundColor = 'transparent';">
                    View All News →
                </a>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl shadow-lg">
                <div class="flex items-center space-x-3 mb-4 border-b pb-3">
                    <span class="w-6 h-6" style="color: var(--core-blue);">📖</span>
                    <h3 class="text-xl font-bold text-gray-800">Resources & Tools</h3>
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="#guidelines" class="flex items-center text-sm text-gray-700 font-medium transition-colors hover:text-blue-800">
                            <span class="w-4 h-4 mr-2" style="color: var(--core-blue); opacity: 0.6;">→</span> Community Contribution Guidelines (Bookstack)
                        </a>
                    </li>
                    <li>
                        <a href="#training" class="flex items-center text-sm text-gray-700 font-medium transition-colors hover:text-blue-800">
                            <span class="w-4 h-4 mr-2" style="color: var(--core-blue); opacity: 0.6;">→</span> Advanced Training Modules (TWB Learning Center)
                        </a>
                    </li>
                    <li>
                        <a href="#support" class="flex items-center text-sm text-gray-700 font-medium transition-colors hover:text-blue-800">
                            <span class="w-4 h-4 mr-2" style="color: var(--core-blue); opacity: 0.6;">→</span> Technical Support & FAQs
                        </a>
                    </li>
                    <li>
                        <a href="#style-guides" class="flex items-center text-sm text-gray-700 font-medium transition-colors hover:text-blue-800">
                            <span class="w-4 h-4 mr-2" style="color: var(--core-blue); opacity: 0.6;">→</span> Language-Specific Style Guides
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl shadow-lg">
                <div class="flex items-center space-x-3 mb-4 border-b pb-3">
                    <div class="p-2 rounded-full" style="background-color: var(--core-blue); opacity: 0.1;">
                        💬
                   </div>
                   <h3 class="text-xl font-bold text-gray-800">Community Survey</h3>
                </div>
                <p class="font-semibold text-gray-700 mb-2">How satisfied are you with the platform's task matching feature?</p>
                <p class="text-sm text-gray-500 mb-4">Your anonymous feedback helps us improve task assignments and efficiency.</p>
                <form onsubmit="alert('Response submitted!'); return false;" class="space-y-3">
                    <div class="flex items-center">
                        <input type="radio" id="radio-v-satisfied" name="survey-response" value="Very Satisfied" class="h-4 w-4 border-gray-300" style="color: var(--core-blue);" required>
                        <label for="radio-v-satisfied" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer w-full p-2 hover:bg-gray-50 rounded">Very Satisfied</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="radio-satisfied" name="survey-response" value="Satisfied" class="h-4 w-4 border-gray-300" style="color: var(--core-blue);" required>
                        <label for="radio-satisfied" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer w-full p-2 hover:bg-gray-50 rounded">Satisfied</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="radio-neutral" name="survey-response" value="Neutral" class="h-4 w-4 border-gray-300" style="color: var(--core-blue);" required>
                        <label for="radio-neutral" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer w-full p-2 hover:bg-gray-50 rounded">Neutral</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="radio-dissatisfied" name="survey-response" value="Dissatisfied" class="h-4 w-4 border-gray-300" style="color: var(--core-blue);" required>
                        <label for="radio-dissatisfied" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer w-full p-2 hover:bg-gray-50 rounded">Dissatisfied</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="radio-v-dissatisfied" name="survey-response" value="Very Dissatisfied" class="h-4 w-4 border-gray-300" style="color: var(--core-blue);" required>
                        <label for="radio-v-dissatisfied" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer w-full p-2 hover:bg-gray-50 rounded">Very Dissatisfied</label>
                    </div>
                    <button type="submit" style="background-color: var(--twb-accent);" class="w-full py-2 text-white font-semibold rounded-lg mt-4 transition duration-300 flex items-center justify-center hover:opacity-90">
                        Submit Response
                    </button>
                </form>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl shadow-lg border-t-4 border-t-gray-400">
                <h3 class="text-xl font-bold text-gray-800 mb-3">Feedback & Suggestions</h3>
                <p class="text-gray-600 mb-4 text-sm">Have ideas for improvement? Share your experience with the platform or suggest a new feature.</p>
                <a href="#feedback-form" class="inline-flex items-center py-2 px-4 border border-transparent text-sm font-medium rounded-full shadow-sm text-white transition-colors" style="background-color: var(--twb-accent);" onmouseover="this.style.backgroundColor = 'var(--core-blue)';" onmouseout="this.style.backgroundColor = 'var(--twb-accent)';">
                    💡 Submit Feedback
                </a>
            </div>
        </div>
    </div>
</div>
</main>

{include file="footer2.tpl"}
