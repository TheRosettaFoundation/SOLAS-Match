{include file='new_header.tpl'}


<div class="container">
<div class="d-flex row justify-content-between mt-5 ">
   

    <div class=" col-sm-12 col-md-4 col-lg-3 ">



     {if isset($user)}
            <h5 class="fw-bold mt-5">{Localisation::getTranslation('index_filter_available_tasks')}
            
            </h5>
            <div>You can only filter for languages that you have chosen as your language pairs in your user profile.</div>
           
            <form method="post" action="{urlFor name="claimed-tasks" options="user_id.$user_id"}">
            <div class="filter-block">
                <div class="filter-title">{Localisation::getTranslation('common_task_type')}</div>
                <select name="taskTypes" id="taskTypes" class="form-select">
                    <option value="0" {if ($selectedTaskType === 0)}selected="selected"{/if}>{Localisation::getTranslation('index_any_task_type')}</option>
                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                        {if $ui['enabled']}
                            <option value="{$ui['type_enum']}" {if ($selectedTaskType === {$ui['type_enum']})}selected="selected"{/if}>{$ui['type_text']}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
            <div class="filter-block">
                <div class="filter-title">{Localisation::getTranslation('common_task_status')}</div>
                <select name="taskStatusFilter" id="taskStatusFilter" class="form-select">
                    <option value="3" {if ($selectedTaskStatus === 3)}selected="selected"{/if}>{Localisation::getTranslation('common_in_progress')}</option>
                    <option value="0" {if ($selectedTaskStatus === 0)}selected="selected"{/if}>{Localisation::getTranslation('common_any_task_status')}</option>
                    <option value="4" {if ($selectedTaskStatus === 4)}selected="selected"{/if}>{Localisation::getTranslation('common_complete')}</option>
                </select>
            </div>
            <div class="filter-block">
                <div class="filter-title">{Localisation::getTranslation('claimed_tasks_ordering')}</div>
                <select name="ordering" id="ordering" class="form-select">
                    <option value="0" {if ($selectedOrdering === 0)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_created_asc')}</option>
                    <option value="1" {if ($selectedOrdering === 1)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_created_desc')}</option>
                    <option value="2" {if ($selectedOrdering === 2)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_deadline_asc')}</option>
                    <option value="3" {if ($selectedOrdering === 3)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_deadline_desc')}</option>
                    <option value="4" {if ($selectedOrdering === 4)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_title_asc')}</option>
                    <option value="5" {if ($selectedOrdering === 5)}selected="selected"{/if}>{Localisation::getTranslation('claimed_tasks_ordering_title_desc')}</option>
                </select>
            </div>
              <div class=" d-grid mt-3 mb-5  ">
            <button class="btn btn-primary" type="submit">
               <img src="{urlFor name='home'}ui/img/setting-5.svg" alt="Con" class="me-1">{Localisation::getTranslation('index_filter_task_stream')}
            </button>
            </div>
        </form>

        {/if}
        
   
    </div>

    <div class="col-sm-12 col-md-8 col-lg-9 mt-4">


            {if isset($topTasks) && count($topTasks) > 0}
            <div class=" d-flex justify-content-start align-items-center mb-3 "> 
                
                     <div>
                        <h3>
                                {if isset($thisUser)}
                                    {if $thisUser->getDisplayName() != ''}
                                        {sprintf(Localisation::getTranslation('claimed_tasks_users_claimed_tasks'), {TemplateHelper::uiCleanseHTML($thisUser->getDisplayName())})}
                                    {else}
                                        {Localisation::getTranslation('claimed_tasks_claimed_tasks')}
                                    {/if}
                                {else}
                                    {Localisation::getTranslation('claimed_tasks_claimed_tasks')}
                                {/if}
                                <small>{Localisation::getTranslation('claimed_tasks_a_list_of_tasks')}</small>
                            </h3>
                                    
                                     
                    </div>
                
            
             </div>   


            <div class="taskPagination">

            {for $count=0 to $itemsPerScrollPage-1}
            {assign var="task" value=$topTasks[$count]}
                    <div class="d-flex justify-content-between mb-4 bg-body-tertiary p-3 rounded-3"  >
                       <div class=" w-100">
                        {assign var="task_id" value=$task->getId()}
                        {assign var="type_id" value=$task->getTaskType()}
                        {assign var="status_id" value=$task->getTaskStatus()}
                        {assign var="task_title" value=$task->getTitle()}
                        {if $taskImages[$task_id]}
                        <div  id="task_{$task_id}">
                        {else}
                        <div  id="task_{$task_id}">
                        {/if}
                            <div class="d-flex justify-content-between mb-2 flex-wrap">
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

                                        <p>
                                        {Localisation::getTranslation('common_status')}: <strong>{if $status_id == 3 && $memsource_tasks[$task_id] && $matecat_urls[$task_id] == ''}Claimed{else}{$taskStatusTexts[$status_id]}{/if}{if $task->get_cancelled()} (Cancelled){/if}</strong>
                                         </p>

                                         <p class="task_details"><div class="process_created_time_utc" style="visibility: hidden">{$created_timestamps[$task_id]}</div></p>


                                         {if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}
                                         
                                            <div class="mb-3  text-muted">
                                                <span class=" ">
                                                    Languages: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getSourceLocale())}  <img src="{urlFor name='home'}ui/img/lang_arr.svg" alt="arrow" class="mx-1" > </strong>
                                                </span>
                                        {/if}
                                            <span>
                                            <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}</strong>
                                            </span>
                                         
                                        
                                            
                                            </div>
                                            <div class="process_deadline_utc d-flex flex-wrap align-items-center text-muted" style="visibility: hidden"> {$deadline_timestamps[$task_id]}</div>
                             </div>
                           

                                <div>
                                        {if $taskImages[$task_id]}
                                        <div id="img_{$task_id}"  >
                                            <img src="{$taskImages[$task_id]}" style ="width:100px ; height:100px">
                                        </div>
                                        {else}
                                            <div id="img_{$task_id}" class="" ></div>
                                        {/if}

                                </div>
                          

                            
                            </div>
                           
                            {if $task->getProjectId() > Settings::get("discourse.pre_discourse") && !preg_match('/^Test.{4}$/', $task_title)}
                            {/if}


                            
                            <div class ="  ">
                                    <div class="d-flex text-body flex-wrap"> <span  class="project" >{$projectAndOrgs[$task_id]}</span> 
                                         
                                    </div>
                                     <div class=" mt-4  ">


                            <p>
                               {if $status_id == 3 && ($type_id == 3 || $type_id == 2 || $type_id == 6)}
                                    {if $matecat_urls[$task_id] != '' && $memsource_tasks[$task_id]}
                                        {if $type_id == 2}
                                            <a href="{$matecat_urls[$task_id]}" target="_blank" class="btn btn-small btn-success">
                                                {if $memsource_tasks[$task_id]}Translate using Phrase TMS{else}{Localisation::getTranslation('task_claimed_translate_using_kato')}{/if}
                                            </a>
                                        {elseif $type_id == 3}
                                            <a href="{$matecat_urls[$task_id]}" target="_blank" class="btn btn-small btn-success">
                                                {if $memsource_tasks[$task_id]}Revise using Phrase TMS{else}{Localisation::getTranslation('task_claimed_proofread_using_kato')}{/if}
                                            </a>
                                        {elseif $type_id == 6}
                                            <a href="{$matecat_urls[$task_id]}" target="_blank" class="btn btn-small btn-success">
                                                Proofread using Phrase TMS
                                            </a>
                                        {/if}
                                    {/if}
                                    {if $allow_downloads[$task_id]}
                                    <a href="{$siteLocation}task/{$task_id}/simple-upload" class="btn btn-small btn-success">
                                        {Localisation::getTranslation('claimed_tasks_submit_completed_task')}
                                    </a>
                                    {else}
                                    {if $show_mark_chunk_complete[$task_id]}
                                    <a href="{$siteLocation}task/{$task_id}/chunk-complete" class="btn btn-small btn-success">
                                        Mark Chunk Complete
                                    </a>
                                    {/if}
                                    {/if}
                                {/if}
                                {if $status_id == 3 && $type_id == 1}
                                    <a href="{$siteLocation}task/{$task_id}/segmentation" class="btn btn-small btn-primary">
                                        {Localisation::getTranslation('claimed_tasks_submit_completed_task')}
                                    </a>
                                {/if}
                                {if $status_id == 3 && $type_id == 4}
                                    <a href="{$siteLocation}task/{$task_id}/desegmentation" class="btn btn-small btn-primary">
                                        {Localisation::getTranslation('claimed_tasks_submit_completed_task')}
                                    </a>
                                {/if}

                               {if $status_id == 3 && TaskTypeEnum::$enum_to_UI[$type_id]['shell_task'] && !empty($shell_task_urls[$task_id])}
                                    <a href="{$shell_task_urls[$task_id]}" target="_blank" class="btn btn-small btn-success">
                                        Work using this URL
                                    </a>
                                {/if}

                                <a href="{$siteLocation}user/task/{$task_id}/reviews" class="btn btn-small btn-primary">
                                    {Localisation::getTranslation('claimed_tasks_task_reviews')}
                                </a>
                                {if $status_id == 3 && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                                    <a href="{$siteLocation}task/{$task_id}/user-feedback" class="btn btn-small btn-danger">
                                        {Localisation::getTranslation('claimed_tasks_unclaim_task')}
                                    </a>
                                {/if}
                                {if $type_id == 2}
                                    {if $proofreadTaskIds[$task_id]}
                                        {if $allow_downloads[$task_id]}
                                        <a href="{$siteLocation}task/{$proofreadTaskIds[$task_id]}/download-task-latest-file/" class="btn btn-small btn-info">
                                            {Localisation::getTranslation('claimed_tasks_download_proofread_task')}
                                        </a>
                                        {/if}
                                    {/if}
                                {/if}
                                {if $parentTaskIds[$task_id]}
                                    <a href="{$siteLocation}task/{$parentTaskIds[$task_id]}/download-task-latest-file/" class="btn btn-small btn-info">
                                        Download Complete Revised Version
                                    </a>
                                {/if}
                                {if $show_memsource_revision[$task_id]}
                                    <a href="{$siteLocation}task/{$show_memsource_revision[$task_id]}/download-task-latest-file/" class="btn btn-small btn-info">
                                        Download Complete Revised Version
                                    </a>
                                {/if}
                                {if $show_memsource_approval[$task_id]}
                                    <a href="{$siteLocation}task/{$show_memsource_approval[$task_id]}/download-task-latest-file/" class="btn btn-small btn-info">
                                        Download Complete Proofread Version
                                    </a>
                                {/if}
                                {if false && ($status_id == 3 || $status_id == 4) && ($type_id == 3 || $type_id == 2)}
                                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSdIEBza8C3RRsP0k75ISPm_urEHa0Fx_A3BGjkYNj8iwl4_mQ/viewform?{if isset($thisUser)}emailAddress={urlencode($thisUser->getEmail())}&{/if}entry.2005620554={$siteLocation}task/{$task_id}/view" class="btn btn-small btn-primary" target="_blank">
                                        TWB Pre-Delivery Checklist
                                    </a>
                                {/if}
                            </p>
                                        
                                        
                                     </div>
                            
                            </div>
                            
                           
                           
                        </div>

                        </div>
                        
                    </div>

                {/for}



            </div> 
            {* pagination begins here *}
            {assign var="url_name" value="claimed-tasks-paged"}
            <ul class="pager pull-left text-decoration-none">
                <div class="pagination-centered" id="ias-pagination">
                    {if $currentScrollPage > 1}
                        <li>
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.1|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="First">&lt;&lt;</a>
                        </li>
                        <li class="ts-previous">
                            {assign var="previous" value=($currentScrollPage - 1)}
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$previous|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="Previous">&lt;</a>
                        </li>
                    {/if}
                    <li>
                        <a href="">{sprintf(Localisation::getTranslation('pagination_page_of'), {$currentScrollPage}, {$lastScrollPage})}</a>
                    </li>
                    {if $currentScrollPage < $lastScrollPage}
                        <li class="ts-next">
                            {assign var="next" value=($currentScrollPage + 1)}
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$next|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="Next" >&gt;</a>
                        </li>
                        <li>
                            <a href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$lastScrollPage|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="Last">&gt;&gt;</a>
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
</div>







    {include file='footer2.tpl'}

