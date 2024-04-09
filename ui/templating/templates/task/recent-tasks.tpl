{include file='new_header.tpl'}

<div class="container">

<span class="hidden">
    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
</span>

{if isset($flash['error'])}
    <br>
    <div class="alert alert-danger">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
    </div>
{/if}

<div class="page-header">
    <h3>
        {if isset($thisUser)}
            {if $thisUser->getDisplayName() != ''}
                {sprintf(Localisation::getTranslation('recent_tasks_users_recent_tasks'), {TemplateHelper::uiCleanseHTML($thisUser->getDisplayName())})}
            {else}
                {Localisation::getTranslation('recent_tasks_recently_viewed_tasks')}
            {/if}
        {else}
            {Localisation::getTranslation('recent_tasks_recently_viewed_tasks')}
        {/if}
        <a href="{urlFor name="home"}" class="btn btn-primary pull-right" role="button">
            <i class="icon-arrow-left icon-white"></i> {Localisation::getTranslation('common_task_stream')}
        </a>
    </h3>
        
</div>

<div id="loading_warning">
    <p>{Localisation::getTranslation('common_loading')}</p>
</div>
<div class="container">
<div class="row ">
   {if isset($recentTasks) && count($recentTasks) > 0}
        <div id="recent-tasks"  class="col-12 col-md-6">
            <div class="ts">
                {for $count=0 to $itemsPerScrollPage-1}
                    {assign var="task" value=$recentTasks[$count]}
                    <div class="ts-task">
                    <div class="d-flex justify-content-between mb-4 bg-body-tertiary p-3 rounded-3"  >
                    <div>
                        {assign var="task_id" value=$task->getId()}
                        {assign var="type_id" value=$task->getTaskType()}
                        {assign var="status_id" value=$task->getTaskStatus()}
                        {assign var="task_title" value=$task->getTitle()}

                        <div class="d-flex justify-content-start mb-2 flex-wrap">
                                <div class="">
                                        <div class="fw-bold fs-3  d-flex align-items-center ">
                                            <a id="task-{$task_id}" href="{$siteLocation}task/{$task_id}/view" class="custom-link ">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($task_title)} 
                                             <img src="{urlFor name='home'}ui/img/question.svg" class="d-none" alt="question_Img" /></a> 
                                        </div>

                                        <div class="d-flex mt-2 mb-3 ">
                                            <span class=" badge rounded-pill border border-2 text-white text-uppercase border-greenBorder border-opacity-25 fs-7 font-bold" style="background-color:{TaskTypeEnum::$enum_to_UI[$type_id]['colour']}">  {TaskTypeEnum::$enum_to_UI[$type_id]['type_text']} </span>
                                                {if $task->getWordCount()}
                                                <span type="button" class=" ms-1 rounded-pill badge bg-quartenary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> {$task->getWordCount()} {TaskTypeEnum::$enum_to_UI[$type_id]['unit_count_text_short']} </span>
                                                {/if}

                                        </div>

                                        <p class="text-muted">
                                        {Localisation::getTranslation('common_status')}: <strong>{if $status_id == 3 && $memsource_tasks[$task_id] && $matecat_urls[$task_id] == ''}Claimed{else}{$taskStatusTexts[$status_id]}{/if}{if $task->get_cancelled()} (Cancelled){/if}</strong>
                                         </p>

                                         <p class="task_details "><div class="process_created_time_utc text-muted" style="visibility: hidden">{$created_timestamps[$task_id]}</div></p>
                                          
                                           {if !empty($completed_timestamps[$task_id])}
                                            <p><div class="process_completed_utc text-muted" style="visibility: hidden">{$completed_timestamps[$task_id]}</div></p>
                                            {/if}

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
                                        
                                            
                                           
                                            <div class="process_deadline_utc d-flex mb-3 flex-wrap align-items-center text-muted" style="visibility: hidden"> {$deadline_timestamps[$task_id]}</div>
                                        </div>
                                </div>
                                {if $task->getProjectId() > Settings::get("discourse.pre_discourse") && !preg_match('/^Test.{4}$/', $task_title)}
                                {/if}
                                
                                <div class="d-flex text-body flex-wrap"> <span  class="project text-muted" >{$projectAndOrgs[$task_id]}</span> 
                                    
                                </div>
                            
                         

                        
                    </div>

                    </div>
                    </div>
                {/for}
               
            </div>

            {* pagination begins here *}
            {assign var="url_name" value="recent-tasks-paged"}
            <ul class="pager pull-left">
                <div class="pagination-centered" id="ias-pagination">
                    {if $currentScrollPage > 1}
                        <li>
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.1"}" title="First">&lt;&lt;</a>
                        </li>
                        <li class="ts-previous">
                            {assign var="previous" value=($currentScrollPage - 1)}
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$previous"}" title="Previous">&lt;</a>
                        </li>
                    {/if}
                    <li>
                        <a href="">{sprintf(Localisation::getTranslation('pagination_page_of'), {$currentScrollPage}, {$lastScrollPage})}</a>
                    </li>
                    {if $currentScrollPage < $lastScrollPage}
                        <li class="ts-next">
                            {assign var="next" value=($currentScrollPage + 1)}
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$next"}" title="Next" >&gt;</a>
                        </li>
                        <li>
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$lastScrollPage"}" title="Last">&gt;&gt;</a>
                        </li>
                    {/if}
                </div>
            </ul>
        </div>
    {else}
        <p>{Localisation::getTranslation('index_no_tasks_available')}</p>
    {/if}
</div>
</div>
<br/>
<div>
    {include file='footer2.tpl'}
</div>
</div>

