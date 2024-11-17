{include file="new_header.tpl"}

    {assign var="task_id" value=$task->getId()}
    {assign var="type_id" value=$task->getTaskType()}

<div class="container-fluid">
    <header class="">
        <div class="container py-2"> 
                <div class="py-2" >
                    <a  class="text-decoration-none text-body fw-bold"  href="/"> Home </a> <i class="fa-solid fa-chevron-right mx-1"> </i>
                    <a  href="{urlFor name="task-view" options="task_id.$task_id"}" class="text-primaryDark fw-bold text-decoration-none"> Task </a>       
                    
                    {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM && !$is_denied_for_task && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                    {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $LINGUIST + $NGO_LINGUIST)) && $user_within_limitations}
                        <i class="fa-solid fa-chevron-right mx-1"> </i>
                    <a class=" text-decoration-none text-body fw-bold" href="{urlFor name="task-claim-page" options="task_id.$task_id"}"> Claim </a>
                    {/if}
                     {/if}
                </div>
        </div>
    </header>

<section class="bg-light-subtle my-2 pb-4"> 
        <div class="container py-5 ">
          <div class="d-flex  flex-wrap justify-content-between">
               <div class="fw-bold primaryDark fs-3">

                {if $task->getTitle() != ''}
                {TemplateHelper::uiCleanseHTML($task->getTitle())} 
                {else}
                {Localisation::getTranslation('common_task')} {$task->getId()}
                {/if}
              
                <small>
                <strong>
                     -
                    {assign var="type_id" value=$task->getTaskType()}
                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                        {if $type_id == $task_type}
                            <span style="color: {$ui['colour']}">{$ui['type_text']} Task</span>
                        {/if}
                    {/foreach}
                </strong>
                 </small>  
                 
                 {if isset($chunks[$task_id])}
                   - <span class="text-quinary fs-5"> [Part {$chunks[$task_id]['low_level'] }</span><span class="text-quinary fs-5" >/{$chunks[$task_id]['number_of_chunks'] }]</span>
                {/if}

                </div>

             <div class="d-flex mt-2 mt-md-0">
               
                  {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER))}
                    <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='btnPrimary text-white me-2'>

                       <img src="{urlFor name='home'}ui/img/edit.svg" alt="edit-icon"  class="me-2">{Localisation::getTranslation('task_view_edit_task_details')}
                    </a>
                {/if}
           
                {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM && !$is_denied_for_task && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                    {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $LINGUIST + $NGO_LINGUIST)) && $user_within_limitations}
                        
                        <a href="{urlFor name="task-claim-page" options="task_id.$task_id"}" class="btnPrimary  text-white">
                        <img src="{urlFor name='home'}ui/img/alarm.svg" alt="alarm-icon" class="me-2" >
                        {Localisation::getTranslation('task_view_download_task')}  </a>
                    {/if}
                {/if}
            </div>
        </div>

                {if $task->getTaskStatus() > TaskStatusEnum::PENDING_CLAIM}
                <p class="alert alert-info alert-dismissible fade show mt-4">
                     {Localisation::getTranslation('task_view_0')}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </p>
                {elseif $is_denied_for_task && $type_id != TaskTypeEnum::TRANSLATION}
                    <p class="alert alert-info  alert-dismissible fade show mt-4">
                        Note: You cannot claim this task, because you have previously claimed the matching translation task.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </p>
                {elseif $is_denied_for_task}
                    <p class="alert alert-info  alert-dismissible fade show mt-4">
                         Note: You cannot claim this task, because you have previously claimed the matching revision or proofreading task.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </p>
                {/if}
            
                {if isset($flash['success'])}
                    <p class="alert alert-success  alert-dismissible fade show mt-4 ">
                    <strong>{Localisation::getTranslation('common_success')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </p>
                {/if}

                {if isset($flash['error'])}
                    <p class="alert alert-error  alert-dismissible fade show mt-4">
                        <strong>{Localisation::getTranslation('common_warning')}:</strong> {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </p>
                {/if}
       </div>

        <div class="container">
            <div class="row d-flex justify-content-between">
                <div class=" col-sm-12  col-md-8 gx-5"> 

                         {include file="task/task.details.tpl"} 

                            {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) && $task->getTaskStatus() < TaskStatusEnum::IN_PROGRESS}
                            <div class="bg-body p-2 border-secondary rounded-3 mt-2">
                              <div class="d-none d-md-flex justify-content-around p-2">
                                   <div class="fs-5 fw-bold w-75"> {Localisation::getTranslation('task_view_assign_label')}</div>
                                      <div class="fs-5 fw-bold w-75"> Remove a user from deny list for this task:</div>
                              </div>

                               <hr class="d-none d-md-block"></hr>
                        
                               <div class=" d-block d-md-flex p-2 fs-6 mt-2">
                                 <div class="w-50" >
                                   <div class="fs-5 fw-bold w-75 mb-4 d-block d-md-none"> {Localisation::getTranslation('task_view_assign_label')}</div>
                                    
                                    <form id="assignTaskToUserForm" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" onsubmit="return confirm('{Localisation::getTranslation("task_view_assign_confirmation")}');">
                                   
                                    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER)}
                                    <input id="input" class="fs-6" type="email" name="userIdOrEmail" placeholder="{Localisation::getTranslation('task_view_assign_placeholder')}"></br>
                                    {/if}
                                    {if !empty($list_qualified_translators)}
                                        <select  name="assignUserSelect" id="assignUserSelect" class="select mt-2" >
                                            <option value="">...</option>
                                            {foreach $list_qualified_translators as $list_qualified_translator}
                                                <option value="{$list_qualified_translator['user_id']}">{TemplateHelper::uiCleanseHTML($list_qualified_translator['name'])}</option>
                                            {/foreach}
                                        </select>
                                        </br>
                                    {/if}
                                       </br>
                                        <a class="btngray-sm mt-2" onclick="$('#assignTaskToUserForm').submit();" href="#">
                                         <img src="{urlFor name='home'}ui/img/add-user.svg" alt="Add user" class="mx-1" /> &nbsp;{Localisation::getTranslation('task_view_assign_button')}
                                        </a>
                                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                    </form> 

                                    </div>
                                    
                                    <div class="w-50">
                                        
                                         <div class="fs-5 fw-bold w-75 mb-4 mt-4  d-block d-md-none"> Remove a user from deny list for this task:</div>
                                        <form id="removeUserFromDenyListForm" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" onsubmit="return confirm('{Localisation::getTranslation("task_view_assign_confirmation")}');" >
                                     
                                        <input type="text" class="fs-6 mb-4" id='input' name="userIdOrEmailDenyList" placeholder="{Localisation::getTranslation('task_view_assign_placeholder')}"><br />
                                        <a class="btngray-sm mt-2" href="#" onclick="$('#removeUserFromDenyListForm').submit();">
                                            <img src="{urlFor name='home'}ui/img/remove-user.svg" alt="remove user" class="mx-1" /> Remove User from deny list
                                        </a>
                                        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                                        </form>
                                    </div>
                               </div> 

                                <a href="{urlFor name="task-search_translators" options="task_id.$task_id"}" class="btngray-sm mt-4 mb-2">
                                     <img src="{urlFor name='home'}ui/img/search-user.svg" alt="arrow" class="mx-1" ></i>&nbsp;Search for Translators
                                </a>
                            </div>
                        {/if}

                        {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) && $task->getTaskStatus() > TaskStatusEnum::PENDING_CLAIM}
                        <div class="mb-2 mt-3">
                            <strong>{Localisation::getTranslation('task_org_feedback_user_feedback')}</strong><hr/>
                            <form id="taskUserFeedback" enctype="application/x-www-form-urlencoded" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}" accept-charset="utf-8">
                                <textarea wrap="soft" style="width: 99%" maxlength="4096" rows="10" name="feedback" placeholder="{Localisation::getTranslation('task_org_feedback_1')}"></textarea>
                                
                                <div class="d-flex justify-content-between mt-2 flex-wrap ">
                                <span>
                                    <button type="submit" value="1" name="revokeTask" class="btngray-sm">
                                        {Localisation::getTranslation('task_org_feedback_2')}
                                    </button>

                                    <label class="checkbox clear_brand">
                                        <input type="checkbox" name="deny_user" value="1" /> Add user to deny list
                                    </label>
                                </span>
                                <span class="" >
                                    <button type="submit" value="Submit" name="submit" class="btngray-sm me-2">
                                         {Localisation::getTranslation('common_submit_feedback')}
                                    </button>
                                    <button type="reset" value="Reset" name="reset" class="btngray-sm">
                                        {Localisation::getTranslation('common_reset')}
                                    </button>
                                </span>
                                </div>
                                {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                            </form>
                        </div>
                        {/if}

                        {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER + $COMMUNITY_OFFICER + $NGO_ADMIN + $NGO_PROJECT_OFFICER)) && $task->getTaskStatus() == TaskStatusEnum::COMPLETE && !TaskTypeEnum::$enum_to_UI[$type_id]['shell_task']}
                            {if !empty($memsource_task)}
                                <p class="mt-4">{Localisation::getTranslation('org_task_review_0')}</p>
                                <p>
                                <a class="btngray-sm" href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}">
                                    {Localisation::getTranslation('org_task_review_download_output_file')}
                                </a>
                                </p>
                            {/if}

                            <h2 class="page-header mt-5">
                                {Localisation::getTranslation('org_task_review_review_this_file')}
                                <span class=" fs-4 text-muted ">{Localisation::getTranslation('org_task_review_1')}</span>
                            </h2>

                            <p>{Localisation::getTranslation('org_task_complete_provide_or_view_review')}</p>
                            <p>
                                <a class="btngray-sm me-2" href="{urlFor name="org-task-review" options="org_id.$org_id|task_id.$task_id"}">
                                   {Localisation::getTranslation('org_task_complete_provide_a_review')}
                                </a>
                                <a class="btngray-sm" href="{urlFor name="org-task-reviews" options="org_id.$org_id|task_id.$task_id"}">
                                   {Localisation::getTranslation('org_task_complete_view_reviews')}
                                </a>
                            </p>
                        {/if}
                </div>

                <div class=" col-sm-12  col-md-4"> 
                            <h4 class="fw-bold mt-4">{Localisation::getTranslation('users_also_viewed')}</h4>
                            {if ($alsoViewedTasksCount>0)}
                                    <div>
                                        {if isset($alsoViewedTasks)}
                                        <div>
                                            <div >
                                                {for $count=0 to $alsoViewedTasksCount-1}
                                                    {assign var="alsoViewedTask" value=$alsoViewedTasks[$count]}
                                                    <div class="">
                                                        {assign var="also_viewed_task_id" value=$alsoViewedTask->getId()}
                                                        {assign var="also_viewed_type_id" value=$alsoViewedTask->getTaskType()}
                                                        {assign var="also_viewed_status_id" value=$alsoViewedTask->getTaskStatus()}
                                                        {assign var="also_viewed_task_title" value=$alsoViewedTask->getTitle()}
                                                        <div class="card mt-4 p-2 fs-5 shadow rounded-1">
                                                            <div class="px-1">
                                                            <a  href="{$siteLocation}task/{$also_viewed_task_id}/view" class="text-decoration-none custom-link fw-bold "> <h4>{TemplateHelper::uiCleanseHTMLNewlineAndTabs($also_viewed_task_title)} </h4> </a>
                                                            </div>
                                                         <div class="mt-2 px-1">
                                                                      {if TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['source_and_target']}
                                                                        <span>
                                                                            {Localisation::getTranslation('common_from')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($alsoViewedTask->getSourceLocale())}</strong>
                                                                        </span>

                                                                         <img src="{urlFor name='home'}ui/img/lang_arr.svg" alt="arrow" class="mx-1" >
                                                                     {/if}
                                                                        <span>
                                                                            {Localisation::getTranslation('common_to')}: <strong>{TemplateHelper::getLanguageAndCountryNoCodes($alsoViewedTask->getTargetLocale())}</strong>
                                                                        </span>
                                                         </div>   
                                                  
                                                        <div class="my-2 d-flex align-items-center">                                                                
                                                                    <span  class=" ms-1 rounded-pill badge  border border-2 border-greenBorder border-opacity-25  text-white font-bold fs-7" style="background-color:{TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['colour']}">{TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['type_text_short']}</span>

                                                                    {if $alsoViewedTask->getWordCount()}
                                                                        <span  class="ms-1 rounded-pill badge  border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7 bg-quartenary"  ">{$alsoViewedTask->getWordCount()} {TaskTypeEnum::$enum_to_UI[$also_viewed_type_id]['unit_count_text_short']}</span>
                                                                    {/if}
                                                                    {if isset($chunksViews[$also_viewed_task_id])}
                                                                        <span  class=" ms-1 rounded-pill badge bg-quinary border border-2 border-quartBorder border-opacity-25  text-white font-bold fs-7"> <span > Part {$chunksViews[$also_viewed_task_id]['low_level'] }</span>/<span>{$chunksViews[$also_viewed_task_id]['number_of_chunks'] } </span></span>
                                                                    {/if}
                                                        </div>
                                                            <p class="px-1 mt-2">
                                                            <span class="text-muted">Due by </span> <strong><span class="convert_utc_to_local_deadline" style="display: inline-block; visibility: hidden">{$deadline_timestamps[$also_viewed_task_id]}</span></strong>
                                                            </p>
                                                            <p  class="project" class="px-1  text-decoration-none">{TemplateHelper::uiCleanseNewlineAndTabs($projectAndOrgs[$also_viewed_task_id])}</p>
                                                        </div>
                                                    </div>
                                                {/for}
                                            </div>
                                        </div>
                                        {/if}
                                    </div>
                            {else}
                                <div>No tasks currently viewed by other Users at this time</div>
                            {/if}
                </div>
            </div>
        </div>
</section>

 <div class="container-sm">
         {if !empty($file_preview_path)}
          <div class="py-4 d-flex  justify-content-between align-items-center flex-wrap"> 
          <div class="fw-bold">
                {Localisation::getTranslation('task_view_source_document_preview')} - {TemplateHelper::uiCleanseHTML($filename)}
          </div>
          <div class="d-flex ">
                        <img src="{urlFor name='home'}ui/img/print.svg" alt="print" id="print" class="mx-4 d-none" />
                         <a class="d-none" href="https://docs.google.com/viewer?url={$file_preview_path}&embedded=true"  download="{$file_preview_path}"  id="download-file"> <img src="{urlFor name='home'}ui/img/download.svg" id="downing" alt="download" /> </a>
          </div>
          </div>
         <div style="padding-bottom:56.25%; position:relative; display:block; width: 100%">
            <iframe width="100%" height="100%" id="iframe"
                src="https://docs.google.com/viewer?url={$file_preview_path}&embedded=true"
                frameborder="0" allowfullscreen="" style="position:absolute; top:0; left: 0">
            </iframe>
         </div>
        {/if}
 </div>
 </div>

{include file="footer2.tpl"}
