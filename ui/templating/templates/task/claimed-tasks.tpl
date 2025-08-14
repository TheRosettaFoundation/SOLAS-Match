{include file='new_header.tpl'}

<div class="d-flex flex-column justify-content-end">
<div class="container flex-grow-1">
<div class="d-flex row justify-content-between mt-3 flex-grow-1">

<span class="d-none">
    <!-- Parameters... -->
    <div id="siteLocation">{$siteLocation}</div>
</span>

    <div class=" col-sm-12 col-md-4 col-lg-3 ">

    {if isset($user)}
            <h5 class="fw-bold mt-5 mb-4">{Localisation::getTranslation('index_filter_available_tasks')}
            </h5>

        <form method="post" action="{urlFor name="claimed-tasks" options="user_id.$user_id"}">
            <div class="filter-block mb-2">

                <select name="taskTypes" id="taskTypes" class="form-select">
                    <option value="0" {if ($selectedTaskType === 0)}selected="selected"{/if}>{Localisation::getTranslation('index_any_task_type')}</option>
                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                        {if $ui['enabled']}
                            <option value="{$ui['type_enum']}" {if ($selectedTaskType === {$ui['type_enum']})}selected="selected"{/if}>{$ui['type_text']}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
            <div class="filter-block mb-2">
                <select name="taskStatusFilter" id="taskStatusFilter" class="form-select mt-1">
                    <option value="3" {if ($selectedTaskStatus === 3)}selected="selected"{/if}>{Localisation::getTranslation('common_in_progress')}</option>
                    <option value="0" {if ($selectedTaskStatus === 0)}selected="selected"{/if}>{Localisation::getTranslation('common_any_task_status')}</option>
                    <option value="4" {if ($selectedTaskStatus === 4)}selected="selected"{/if}>{Localisation::getTranslation('common_complete')}</option>
                </select>
            </div>
            <div class="filter-block">
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
            <button class="btn btn-primary text-white align-middle" type="submit">
               <img src="{urlFor name='home'}ui/img/setting-5.svg" alt="Con" class="me-2">{Localisation::getTranslation('index_filter_task_stream')}
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
                                     {if isset($chunks[$task_id])}
                                         <span  class=" ms-1 rounded-pill badge bg-quinary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> <span> Part {$chunks[$task_id]['low_level'] }</span>/<span>{$chunks[$task_id]['number_of_chunks'] } </span></span>
                                     {/if}
                                 </div>

                                 {if TaskTypeEnum::$enum_to_UI[$type_id]['source_and_target']}
                                     <div class="mb-3 text-muted">
                                         <span>
                                             <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getSourceLocale())} <img src="{urlFor name='home'}ui/img/lang_arr.svg" alt="arrow" class="mx-1" ></strong>
                                         </span>
                                         <span>
                                            <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}</strong>
                                            (<strong>{if $status_id == 3 && $memsource_tasks[$task_id] && $matecat_urls[$task_id] == ''}Claimed{else}{$taskStatusTexts[$status_id]}{/if}{if $task->get_cancelled()} (Cancelled){/if}</strong>)
                                         </span>
                                     </div>
                                 {else}
                                     <div class="mb-3 text-muted">
                                        <span>
                                            <strong>{TemplateHelper::getLanguageAndCountryNoCodes($task->getTargetLocale())}</strong>
                                            (<strong>{if $status_id == 3 && $memsource_tasks[$task_id] && $matecat_urls[$task_id] == ''}Claimed{else}{$taskStatusTexts[$status_id]}{/if}{if $task->get_cancelled()} (Cancelled){/if}</strong>)
                                        </span>
                                     </div>
                                 {/if}

                                 {if !empty($completed_timestamps[$task_id])}
                                     <p><div class="process_completed_utc text-muted" style="visibility: hidden">{$completed_timestamps[$task_id]}</div></p>
                                 {/if}

                                 <div><div class="process_deadline_utc d-flex mb-3 flex-wrap align-items-center text-muted" style="visibility: hidden"> {$deadline_timestamps[$task_id]}</div><span> or earlier, if possible<span></div>
                             </div>
                        </div>

                        {if $task->getProjectId() > Settings::get("discourse.pre_discourse") && !preg_match('/^Test.{4}$/', $task_title)}
                        {/if}

                        <div class="d-flex text-body flex-wrap"> <span  class="project text-muted" >{$projectAndOrgs[$task_id]}</span>
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
                                    <a href="{$siteLocation}task/{$task_id}/desegmentation" class="btn btn-small btn-primary text-white">
                                        {Localisation::getTranslation('claimed_tasks_submit_completed_task')}
                                    </a>
                                {/if}

                               {if $status_id == 3 && TaskTypeEnum::$enum_to_UI[$type_id]['shell_task'] && !empty($shell_task_urls[$task_id])}
                                    <a href="{$shell_task_urls[$task_id]}" target="_blank" class="btn btn-small btn-success mt-2 mt-md-0">
                                        Work using this URL
                                    </a>
                                {/if}

                                <a href="{$siteLocation}user/task/{$task_id}/reviews" class="btn btn-small btn-primary mt-2 mt-md-0 text-white">
                                    {Localisation::getTranslation('claimed_tasks_task_reviews')}
                                </a>
                                {if $status_id == 3 && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                                    <a href="{$siteLocation}task/{$task_id}/user-feedback" class="btn btn-small btn-danger mt-2 mt-md-0">
                                        {Localisation::getTranslation('claimed_tasks_unclaim_task')}
                                    </a>
                                {/if}
                                {if $type_id == 2}
                                    {if $proofreadTaskIds[$task_id]}
                                        {if $allow_downloads[$task_id]}
                                        <a href="{$siteLocation}task/{$proofreadTaskIds[$task_id]}/download-task-latest-file/" class="btn btn-small btn-info mt-2 mt-md-0">
                                            {Localisation::getTranslation('claimed_tasks_download_proofread_task')}
                                        </a>
                                        {/if}
                                    {/if}
                                {/if}
                                {if $parentTaskIds[$task_id]}
                                    <a href="{$siteLocation}task/{$parentTaskIds[$task_id]}/download-task-latest-file/" class="btn btn-small mt-2 mt-md-0 btn-info">
                                        Download Complete Revised Version
                                    </a>
                                {/if}
                                {if $show_memsource_revision[$task_id]}
                                    <a href="{$siteLocation}task/{$show_memsource_revision[$task_id]}/download-task-latest-file/" class="btn btn-small mt-2 mt-md-0 btn-info">
                                        Download Complete Revised Version
                                    </a>
                                {/if}
                                {if $show_memsource_approval[$task_id]}
                                    <a href="{$siteLocation}task/{$show_memsource_approval[$task_id]}/download-task-latest-file/" class="btn btn-small mt-2 mt-md-0 btn-info">
                                        Download Complete Proofread Version
                                    </a>
                                {/if}
                                {if false && ($status_id == 3 || $status_id == 4) && ($type_id == 3 || $type_id == 2)}
                                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSdIEBza8C3RRsP0k75ISPm_urEHa0Fx_A3BGjkYNj8iwl4_mQ/viewform?{if isset($thisUser)}emailAddress={urlencode($thisUser->getEmail())}&{/if}entry.2005620554={$siteLocation}task/{$task_id}/view" class="btn btn-small btn-primary mt-2 mt-md-0" target="_blank">
                                        TWB Pre-Delivery Checklist
                                    </a>
                                {/if}

                                    {if $task->getProjectId() > Settings::get("discourse.pre_discourse") && !preg_match('/^Test.{4}$/', $task_title)}
                                    <a class="btn btn-grayish mt-2 mt-md-0 "href="https://community.translatorswb.org/t/{$discourse_slug[$task_id]}" target="_blank">Ask in the Forum</a>
                                    {/if}
                            </p>
                        </div>
                    </div>
                </div>
            {/for}

            {assign var="url_name" value="claimed-tasks-paged"}
            <div class="d-flex justify-content-start">
                <div class="d-flex">
                    {if $currentScrollPage > 1}
                        <div>
                            <a class="custom-link me-4" href="{urlFor name="$url_name" options="user_id.$user_id|page_no.1|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="First">&lt;&lt;</a>
                        </div>
                        <div class="ts-previous me-2 text-white">
                            {assign var="previous" value=($currentScrollPage - 1)}
                            <a  class="custom-link " href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$previous|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="Previous">&lt;</a>
                        </div>
                    {/if}
                    <div>
                        <a href="" class="custom-link mx-4">{sprintf(Localisation::getTranslation('pagination_page_of'), {$currentScrollPage}, {$lastScrollPage})}</a>
                    </div>
                    {if $currentScrollPage < $lastScrollPage}
                        <div class="ts-next">
                            {assign var="next" value=($currentScrollPage + 1)}
                            <a   class=" custom-link me-4" href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$next|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="Next" >&gt;</a>
                        </div>
                        <div>
                            <a  class="custom-link" href="{urlFor name="$url_name" options="user_id.$user_id|page_no.$lastScrollPage|tt.$selectedTaskType|ts.$selectedTaskStatus|o.$selectedOrdering"}" title="Last">&gt;&gt;</a>
                        </div>
                    {/if}
                </div>
            </div>
            </div> 
    {else}
            <p>
                   <p>{Localisation::getTranslation('index_no_tasks_available')}</p>
            </p>
    {/if}

    </div> 
</div>
</div>

{include file='footer2.tpl'}
