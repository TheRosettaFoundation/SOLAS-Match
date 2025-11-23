{include file="new_header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

<style>
a:hover{
    text-decoration:none !important;
}
.carousel {
    position: relative;
    margin-bottom: 20px;
    line-height: 1;
}
.clearheader{
    color:#143878 !important;
}
h3{
    font-size:12px;
}
@media (min-width: 400px ){
    h3{
        font-size: 16px;
    } 
}
@media (min-width: 900px ){
    h3{
        font-size: 24px;
    } 
}
.car{
    display:none ;
}
@media (min-width: 700px ){
    .car{
      display:block;
    } 
}
.twbheader{
    color:#e8991c !important;
}
.top-left {
  position: absolute;
  top: 8px;
  left: 23px;
  color:white;
  z-index:10;
}
.btn-block {
    width: 120px !important;
}
.btn-home-slider{
    display: inline-block;
    height: 30px;
}
#globe{
    color:white;
}
.login{
    color:white !important;
    font-weight:bold;
    text-decoration:underline;
}
.button {
  background-color: #143878; /* Green */
  border: none;
  color: white;
  padding: 5px 10px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 14px;
  margin: 4px 2px;
  transition-duration: 0.4s;
  cursor: pointer;
}
.button a{
    color:#FFFFFF;
    font-weight:bold;
}
.button_join {
  background-color: #f89406; 
  border: none;
  color: white;
  padding: 5px 35px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 14px;
  margin: 4px 2px;
  transition-duration: 0.4s;
  cursor: pointer;
}
.button_join a{
    color:#FFFFFF;
    font-weight:bold;
}
.button1 {
  background-color: #f89406; 
  color: #FFF; 
  border: 2px solid #f89406;
}
.button1:hover {
  background-color: #cb7500;
  color: white;
}
.button2 {
  background-color: #143878; 
  color: #FFFFFF; 
  border: 2px solid #143878;
}
.button2:hover {
  background-color: #0e2754;
  color: white;
}
a:hover{
    text-decoration:underline;
}
</style>

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

<div class ="container-fluid">

<div class="d-flex row justify-content-between mt-5 ">

    <div class=" col-sm-12 col-md-4 col-lg-3 ">

    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
        <form  method="post" action="{urlFor name="org-search"}" accept-charset="utf-8" class="needs-validation" novalidate> 
            <div class="d-flex mb-4 ">
             <input type="text" class="  form-control "  style="outline:none ;" name="search_name" id="search_name" placeholder= "search organizations"> 
                <button class="position-relative border-0 bg-primary rounded-end-2" style="left:-3px ;" type="submit" name="submit">
                        <img src="{urlFor name='home'}ui/img/search.svg" alt="search" >
                </button>
            </div>
         </form>
    {/if}

            <h5 class="fw-bold mt-5">{Localisation::getTranslation('index_filter_available_tasks')}
            </h5>
            <div>You can only filter for languages that you have chosen as your language pairs in your user profile.</div>
            <form method="post" action="{urlFor name="home"}" class="needs-validation" novalidate>
	                    <div class="mt-4">
                            <div class="mb-3">
                                    <select name="taskTypes" id="taskTypes" class="form-select" required >
	                                    <option value="0" {if ($selectedTaskType === 0)}selected="selected"{/if}>{Localisation::getTranslation('index_any_task_type')}</option>
                                      <!-- <option value="1" {if ($selectedTaskType === 1)}selected="selected"{/if}>{Localisation::getTranslation('common_segmentation')}</option> -->
	                                    <option value="2" {if ($selectedTaskType === 2)}selected="selected"{/if}>{Localisation::getTranslation('common_translation')}</option>
	                                    <option value="3" {if ($selectedTaskType === 3)}selected="selected"{/if}>{Localisation::getTranslation('common_proofreading')}</option>
                                      <!-- <option value="4" {if ($selectedTaskType === 4)}selected="selected"{/if}>{Localisation::getTranslation('common_desegmentation')}</option> -->
                                      <option value="6" {if ($selectedTaskType === 6)}selected="selected"{/if}>Proofreading and Approval</option>
	                                 </select>
                                     <div class="invalid-feedback"> Please select task type </div>
                            </div>
                            <div class="mb-3">
                                            <select name="sourceLanguage" ID="sourceLanguage" class="form-select" required>
                                                <option value="0" {if ($selectedSourceLanguageCode === 0)}selected="selected"{/if}>Any Language Pair</option>
                                                {foreach $active_languages as $lang}
                                                    {assign var="pair" value="`$lang['ls_code']`_`$lang['lt_code']`"}
                                                    <option value="{$pair}" {if ($selectedSourceLanguageCode === $pair)}selected="selected"{/if}>{$lang['ls_name']} to {$lang['lt_name']}</option>
                                                {/foreach}
                                            </select>
                            </div>
                            <div class"mb-3">
                            </div>
	                    </div>

                <div class=" d-grid mt-3 mb-5  ">
                    <button class="btn btn-primary" type="submit">
                        <img src="{urlFor name='home'}ui/img/setting-5.svg" alt="Con" class="me-1">
                        <span class="text-white"> Apply filters</span>
                    </button>                
                </div>
            </form>
   
    </div>

    <div class="col-sm-12 col-md-8 col-lg-9 mt-4">

                    {if isset($topTasks) && count($topTasks) > 0}
            <div class=" d-flex justify-content-end align-items-center mb-3 "> 
                     <div>
                        <a href="{urlFor name="recent-tasks" options="user_id.$user_id"}"  class="btn btn-primary text-white">
	                        {Localisation::getTranslation('recent_tasks_recently_viewed_tasks')}
	                    </a>
                    </div>
             </div>        
            <div class="taskPagination">
                {for $count=0 to $itemsPerScrollPage-1}
                    {assign var="task" value=$topTasks[$count]}
                    <div class="d-flex justify-content-between mb-4 bg-body-tertiary p-3 rounded-3"  >
                       <div class=" w-100">
                        {assign var="task_id" value=$task->getId()}
                        {assign var="type_id" value=$task->getTaskType()}
                        {assign var="task_title" value=$task->getTitle()}
                        {if $taskImages[$task_id]}
                        <div  id="task_{$task_id}">
                        {else}
                        <div  id="task_{$task_id}">
                        {/if}
                            <div class="d-flex justify-content-between mb-2 flex-wrap">
                                <div class="w-75">
                                        <div class="fw-bold fs-3  d-flex align-items-center ">
                                            <a id="task-{$task_id}" href="{$siteLocation}task/{$task_id}/view" class="custom-link w-75 text-wrap ">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task_title)} 
                                             <img src="{urlFor name='home'}ui/img/question.svg" class="d-none" alt="question_Img" /></a> 
                                        </div>

                                        <div class="d-flex mt-2 mb-3 ">
                                            <span class=" badge rounded-pill border border-2 text-white text-uppercase border-greenBorder border-opacity-25 fs-7 font-bold" style="background-color:{TaskTypeEnum::$enum_to_UI[$type_id]['colour']}">  {TaskTypeEnum::$enum_to_UI[$type_id]['type_text']} </span>
                                                {if $task->getWordCount()}
                                                <span  class=" ms-1 rounded-pill badge bg-quartenary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> {$task->getWordCount()} {TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text_short']} </span>
                                                {/if}
                                                {if isset($chunks[$task_id])}
                                                    <span  class=" ms-1 rounded-pill badge bg-quinary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> <span> Part {$chunks[$task_id]['low_level'] }</span>/<span>{$chunks[$task_id]['number_of_chunks'] } </span></span>
                                                {/if}
                                        </div>

                                         {if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}
                                            <div class="mb-3  text-muted">
                                                <span class=" ">
                                                    Languages: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getSourceLocale())}  <img src="{urlFor name='home'}ui/img/lang_arr.svg" alt="arrow" class="mx-1" > </strong>
                                                </span>
                                            <span>
                                            <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}</strong>
                                            </span>
                                            </div>
                                         {else}
                                         <div class="mb-3  text-muted">
                                                <span class=" ">
                                                    Language:
                                                </span>
                                            <span>
                                            <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}</strong>
                                            </span>
                                            </div>
                                         {/if}

                                         {if $max_translation_deadlines[$task_id]}
                                         <div class="process_deadline_utc_non_timezone d-flex flex-wrap align-items-center text-muted" style="visibility: hidden">{$max_translation_deadlines[$task_id]}</div>
                                         {/if}

                                        <div class="process_deadline_utc d-flex flex-wrap align-items-center text-muted" style="visibility: hidden"> {$deadline_timestamps[$task_id]}</div>
                             </div>

                                <div>
                                        {if $taskImages[$task_id]}
                                        <div class="mt-2 md:mt-0" id="img_{$task_id}"  >
                                            <img src="{$taskImages[$task_id]}" style ="width:100px ; height:100px">
                                        </div>
                                        {else}
                                            <div id="img_{$task_id}" class="" ></div>
                                        {/if}
                                </div>

                            </div>

                            {if $task->getProjectId() > Settings::get("discourse.pre_discourse") && !preg_match('/^Test.{4}$/', $task_title)}
                            {/if}

                            <div class ="d-flex justify-content-between align-items-center flex-wrap  ">
                                    <div class="d-flex  flex-wrap text-muted w-75"> <span  class="project" >{$projectAndOrgs[$task_id]}</span> 
                                    </div>
                                     <div class="d-flex justify-content-end flex-wrap mt-2 mt-sm-4 mt-md-0 ">
                                        <a class="btn btn-secondary fs-5 px-3"  href="{$siteLocation}task/{$task_id}/view">View Task</a>
                                     </div>
                            </div>

                        </div>

                        </div>

                    </div>
                {/for}
            </div>
        </div>

            <ul class="flex-row d-flex justify-content-center list-unstyled flex-wrap text-secondary pagination mt-1 mt-md-0">
                    {assign var="url_nam" value="task_stream_AJAX"}
                    {if $page_count> 6}
                        {assign var="count" value= 6}
                    {else}
                        {assign var="count" value= $page_count}
                    {/if}    

                    {if $page_count>1}
                    <li class="first mx-2 border border-dark-subtle rounded-3 py-1 px-2 mt-1 mt-md-0" >
                            <a class=" text-decoration-none link-body-emphasis fs-6" href="{urlFor name="$url_nam" options="page_no.1|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}">FIRST</a></li>
                    <li class="d-flex align-items-center mx-2 bg-gray align-middle opacity-50 border border-dark-subtle rounded-3 mt-1 mt-md-0 py-1 px-2" id="previous"  >
                            <a class="d-flex align-items-center text-decoration-none link-body-emphasis fs-6" href="{urlFor name="$url_nam" options="page_no.1|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}">  <i class="fa-solid fa-caret-left me-1"></i>PREV</a></li>
                    {else}
                    <li class=" d-none first mx-2 border border-dark-subtle rounded-3 py-1 px-2 mt-1 mt-md-0" >
                            <a class=" text-decoration-none link-body-emphasis fs-6" href="{urlFor name="$url_nam" options="page_no.1|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}">FIRST</a></li>
                    <li class="d-none d-flex align-items-center mx-2 bg-gray align-middle opacity-50 border border-dark-subtle rounded-3 mt-1 mt-md-0 py-1 px-2" id="previous"  >
                            <a class="d-flex align-items-center text-decoration-none link-body-emphasis fs-6" href="{urlFor name="$url_nam" options="page_no.1|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}">  <i class="fa-solid fa-caret-left me-1"></i>PREV</a></li>
                    {/if}

                    {for $page=1 to $count}
                            <li {if $page==1 } class="mx-2 bg-primary border border-dark-subtle mt-1 mt-md-0 rounded-3 py-1 px-2 listPage text-primary" {else} class="mx-2 border border-dark-subtle mt-1 mt-md-0 rounded-3 py-1 px-2 listPage" {/if}>
                            <a  class="page text-decoration-none link-body-emphasis fs-6" id={$page} href="{urlFor name="$url_nam" options="page_no.$page|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}">{$page}</a></li>
                    {/for}
                    {if $page_count>1}
                    <li class="mx-2 d-flex align-items-center mx-2  border border-dark-subtle mt-1 mt-md-0 rounded-3 py-1 px-2" id="next">
                            <a class=" d-flex align-items-center text-decoration-none link-body-emphasis fs-6" href="{urlFor name="$url_nam" options="page_no.1|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}">NEXT <i class="fa-solid fa-caret-right ms-1"></i></a></li> 
                     <li class=" last mx-2  border border-dark-subtle mt-1 mt-md-0 rounded-3 py-1 px-2 " >
                            <a class="pageCount text-decoration-none link-body-emphasis fs-6" id={$page_count}  href="{urlFor name="$url_nam" options="page_no.$page_count|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}"> LAST</a></li>
                    {else}
                    <li class="d-none mx-2 d-flex align-items-center mx-2  border border-dark-subtle mt-1 mt-md-0 rounded-3 py-1 px-2" id="next">
                            <a class=" d-flex align-items-center text-decoration-none link-body-emphasis fs-6" href="{urlFor name="$url_nam" options="page_no.1|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}">NEXT <i class="fa-solid fa-caret-right ms-1"></i> </a></li> 
                     <li class="d-none last mx-2  border border-dark-subtle mt-1 mt-md-0 rounded-3 py-1 px-2 " >
                            <a class="pageCount text-decoration-none link-body-emphasis fs-6" id={$page_count}  href="{urlFor name="$url_nam" options="page_no.$page_count|tt.$selectedTaskType|sl.$selectedSourceLanguageCode|tl.$selectedTargetLanguageCode"}"> LAST</a></li>
                    {/if}
            </ul>
        {else}
            <p>
                {if !$org_admin}
                There are currently no tasks available for your language combinations. However, some may come up soon, so don't forget to <a href="https://community.translatorswb.org/t/signing-up-for-kato-platform-email-notifications/121" target="_blank">set up email alerts</a> <br/> to be notified of new available tasks. Meanwhile, you can:
                <ol>
                <li>
                    <a href="https://translatorswithoutborders.org/blog/" target="_blank" class="text-primary">Learn more</a> about the work we do
                </li>
                <li>
                    <a href="https://community.translatorswb.org/t/creating-and-personalizing-your-kato-community-profile/3048" class="text-primary" target="_blank">Register</a> and browse our forum
                </li>
                <li>
                    New to TWB? Have a look at our <a href="https://community.translatorswb.org/t/welcome-pack-for-kato-translators/3138" target="_blank" class="text-primary">Translator's Toolkit</a> to find out how to get started with us.
                </li>
                </ol>
                    <p>
                    For any questions or comments, please email <a href="mailto:translators@translatorswithoutborders.org" target="_blank" class="text-primary">translators@translatorswithoutborders.org</a>
                    </p>
                {else}
                    Since you are not a translator, there are no tasks here. Click on <a href="https://twbplatform.org/org/dashboard/" class="text-prinary">your organization's Dashboard</a>
                {/if}
            </p>
        {/if}

    </div>
</div>

<h1 class="end of container"></h1>

</div>

</div>

</div>

{include file="footer2.tpl"}
