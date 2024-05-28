{include file="new_header.tpl"}

{assign var="task_id" value=$task->getId()}
{assign var="task_status_id" value=$task->getTaskStatus()}

<div class="container-fluid">

    <header class="">

        <div class="container py-2"> 

                <div class="py-2" >

                    <a  class="text-decoration-none text-body fw-bold"  href="/"> Home </a>  <i class="fa-solid fa-chevron-right mx-1"> </i>
        
                    <a  href="{urlFor name="task-view" options="task_id.$task_id"}" class="text-primaryDark fw-bold text-decoration-none"> Task </a>       
                    
               
                </div>

                    {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}                 
                    <div class="alert alert-info alert-dismissible fade show mt-4">
                        <h3>
                            <div>{Localisation::getTranslation('common_note')} 
                            {if $task_status_id == TaskStatusEnum::IN_PROGRESS}
                            <span>This task is in progress. {Localisation::getTranslation('task_alter_1')}</span>
                            {else if $task_status_id == TaskStatusEnum::CLAIMED}
                                <span>This task has been claimed. {Localisation::getTranslation('task_alter_1')}</span>
                            {else if $task_status_id == TaskStatusEnum::COMPLETE}
                                <span>This task has been completed. {Localisation::getTranslation('task_alter_you_can_only_edit')}</span>
                            </div>
                            {/if}
                        </h3>            
                    
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    {/if}
                 

        </div>

     
            

    </header>

<section class="bg-light-subtle my-2 pb-4"> 

    <div class="container py-5">

        <div class="d-flex  flex-wrap justify-content-between mb-4"> 


            <h3 >
            <span class="fw-bold">{Localisation::getTranslation('common_task')} {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}<span>
            
            </h3>

            <div>

                <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='btnPrimary text-white'>
                {Localisation::getTranslation('task_alter_view_task_details')}
                </a>
            
            
            </div>
        
        
        </div>


        <form method="post" action="{urlFor name="task-alter" options="task_id.$task_id"}"  accept-charset="utf-8">
        <div class="table-responsive">    
        <table class="w-100 ">
           <tbody class="mx-4">

                <tr class="d-flex justify-content-between flex-wrap " >
                <td class="" >
                    <div class="mb-3">
                        <label for="title" class="form-label"><strong>{Localisation::getTranslation('common_title')}</strong></label>
                        <textarea class="form-control" cols="1" rows="4" id="title" name="title" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} style="width: 400px">{$task->getTitle()|escape:'html':'UTF-8'}</textarea>
                    </div>
                    <div class="mb-3" >
                        <label for="impact" class="form-label"><strong>{Localisation::getTranslation('common_task_comment')}</strong></label>
                        <textarea class="form-control" cols="1" rows="6" id="impact" name="impact">{$task->getComment()|escape:'html':'UTF-8'}</textarea>
                    </div>
                    
                    {* <div class="mb-3">
                        <label for="deadline" style="font-size: large"><strong>{Localisation::getTranslation('common_deadline')}</strong></label>
                        {if $deadline_error != ''}
                            <div class="alert alert-error">
                                {$deadline_error}
                            </div>
                        {/if}
                        <p>
                           
                            <input class="hasDatePicker" type="text" id="deadline_field" name="deadline_field" value="{if isset($deadlineDateTime)}{$task->getDeadline()}{/if}" style="width: 400px" />
                            <input type="hidden" name="deadline" id="deadline" />
                        </p>
                    </div> *}

                  
                    
                    <p>
                    {assign var="deadlineDateTime" value=$task->getDeadline()}
                    <label>24hr Date-Time:</label>
                        <div class="input-group date" id="id_1">
                        <input type="text" value="" class="form-control" required/>
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                        </span>
                        </div>

                    </p>
                 
           
                    {if $roles & ($SITE_ADMIN + $PROJECT_OFFICER)}
                    <div >
                        <label for="required_qualification_level" class="form-label"><strong>{Localisation::getTranslation('required_qualification_level')}</strong></label>
                        <select class="form-control" name="required_qualification_level" id="required_qualification_level" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} style="width: 400px">
                            <option value="1" {if $required_qualification_level == 1}selected="selected"{/if}>{Localisation::getTranslation('user_qualification_level_1')}</option>
                            <option value="2" {if $required_qualification_level == 2}selected="selected"{/if}>{Localisation::getTranslation('user_qualification_level_2')}</option>
                            <option value="3" {if $required_qualification_level == 3}selected="selected"{/if}>{Localisation::getTranslation('user_qualification_level_3')}</option>
                        </select>
                    </div>
                    {/if}
                </td>
                <td class="ms-0 md:ms-4">
                    <div>
                        <label for="publishTask" class="form-lable"><strong>{Localisation::getTranslation('common_publish_task')}</strong></label>
                        <p class="desc">{Localisation::getTranslation('common_if_checked_tasks_will_appear_in_the_tasks_stream')}</p>
                        <input type="checkbox" id="publishTask" name="publishTask" value="{$task->getPublished()}" {$publishStatus} {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if}/>
                    </div>
                    {if $showRestrictTask}
                    <div >
                        <label for="restrictTask" style="font-size: large"><strong>{Localisation::getTranslation('restrict_task')}</strong></label>
                        <p class="desc">{Localisation::getTranslation('restrict_task_long')}</p>
                        <input class="form-check-input" type="checkbox" name="restrictTask" value="1" {$restrictTaskStatus} />
                    </div>
                    {/if}
                    {if !empty($languages)}
                    <p>
                        <label for="target" class="form-label"><strong>{Localisation::getTranslation('common_target_language')}</strong></label>
                        <select class="form-control" name="target" id="target" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} style="width: 400px">
                            {foreach $languages as $language}
                                {if $task->getTargetLocale()->getLanguageCode() == $language->getCode()}
                                        <option value="{$language->getCode()}" selected="selected" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} >{$language->getName()}</option>
                                {else}
                                    <option value="{$language->getCode()}" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} >{$language->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>
                    </p>
                    <p>
                    {if isset($countries)}
                        <select class="form-control" name="targetCountry" id="targetCountry" {if $task_status_id > TaskStatusEnum::PENDING_CLAIM}disabled{/if} style="width: 400px">
                            {foreach $countries as $country}
                                {if $task->getTargetLocale()->getCountryCode() == $country->getCode()}
                                    <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                                {else}
                                    <option value="{$country->getCode()}">{$country->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>
                    {/if}
                    </p>
                    {/if}
                    </p>

                    {if !is_null($word_count_err)}
                        <div class="alert alert-danger">
                            {$word_count_err}
                        </div>
                    {/if} 
                    </p>

                    <label for="word_count" class="form-label mb-3"><strong>{TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['unit_count_text']}</strong></label>
                    <input class="form-control" type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}" {if !($roles & ($SITE_ADMIN + $PROJECT_OFFICER))}disabled{/if} style="width: 400px" />
                    
                    <label for="word_count_partner_weighted" class="form-label mt-2"><strong>Partner weighted {TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['unit_count_text']}</strong></label>
                    <input class="form-control" type="text" name="word_count_partner_weighted" id="word_count_partner_weighted" maxlength="6" value="{$task->get_word_count_partner_weighted()}" {if !($roles & ($SITE_ADMIN + $PROJECT_OFFICER))}disabled{/if} style="width: 400px" />

                    {if ($roles & ($SITE_ADMIN + $PROJECT_OFFICER) || in_array($project->getOrganisationId(), $ORG_EXCEPTIONS) && $roles & ($NGO_ADMIN + $NGO_PROJECT_OFFICER)) && TaskTypeEnum::$enum_to_UI[$task->getTaskType()]['shell_task']}
                    <p style="margin-bottom:40px;"/>
                    <label for="shell_task_url" class="form-label"><strong>Shell Task Work URL</strong></label>
                    <input  type="text" name="shell_task_url" id="shell_task_url" value="{$shell_task_url}" class="form-control" />
                    {/if}
                </td>             
            </tr>



             
            {if !empty($projectTasks)}
            <tr>
                <td>
                    <h2>{Localisation::getTranslation('common_task_prerequisites')}</h2>
                    <p class="desc">{Localisation::getTranslation('common_assign_prerequisites_for_this_task_if_any')}</p>
                    <p>
                        {Localisation::getTranslation('common_these_are_tasks_that_must_be_completed_before_the_current_task_becomes_available')}
                    </p>
                    <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all;" width="100%" >
                        <thead>
                            <th>{Localisation::getTranslation('common_assign')}</th>
                            <th>{Localisation::getTranslation('common_title')}</th>
                            <th>{Localisation::getTranslation('common_source_language')}</th>
                            <th>{Localisation::getTranslation('common_target_language')}</th>
                            <th>{Localisation::getTranslation('common_type')}</th>
                            <th>{Localisation::getTranslation('common_status')}</th>
                        </thead>
                        {assign var="i" value=0}
                        {foreach $projectTasks as $projectTask}                                    
                            {assign var="type_id" value=$projectTask->getTaskType()}
                            {assign var="status_id" value=$projectTask->getTaskStatus()}
                            {assign var="task_id" value=$projectTask->getId()}
                            <tr style="overflow-wrap: break-word;">
                                <td>
                                    {if $task_status_id > TaskStatusEnum::PENDING_CLAIM} 
                                        <input type="checkbox" name="preReq_{$i}" value="{$task_id}" disabled                                               
                                        {if in_array($task_id, $thisTaskPreReqIds)}
                                            checked="true" />
                                        {else}
                                            />
                                        {/if} 
                                    {else}
                                        {*
                                        {if $tasksEnabled[$task_id]}
                                            <input type="checkbox" name="preReq_{$i}" value="{$task_id}"
                                        {else}
                                            <input type="checkbox" name="preReq_{$i}" value="{$task_id}" disabled
                                        {/if}
                                        *}
                                        <input type="checkbox" name="preReq_{$i}" value="{$task_id}"
                                        {if in_array($task_id, $thisTaskPreReqIds)}
                                            checked="true" />
                                        {else}
                                            />
                                        {/if} 
                                    {/if}
                                    {assign var="i" value=$i+1}
                                </td>
                                <td>
                                    <a href="{urlFor name="task-view" options="task_id.$task_id"}">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($projectTask->getTitle())}</a>
                                </td>
                                <td>{TemplateHelper::getTaskSourceLanguage($projectTask)}</td>  
                                <td>{TemplateHelper::getTaskTargetLanguage($projectTask)}</td>
                                <td>                                            
                                    {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                                        {if $type_id == $task_type}
                                            <span style="color: {$ui['colour']}">{$ui['type_text']}</span>
                                        {/if}
                                    {/foreach}
                                </td>
                                <td>                                            
                                    {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                        {Localisation::getTranslation('common_waiting')}
                                    {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                        {Localisation::getTranslation('common_unclaimed')}
                                    {elseif $status_id == TaskStatusEnum::CLAIMED}
                                        Claimed
                                    {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                        <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">{Localisation::getTranslation('common_in_progress')}</a>
                                    {elseif $status_id == TaskStatusEnum::COMPLETE}
                                        {if !empty($allow_downloads[$task_id])}<a href="{urlFor name="home"}task/{$task_id}/download-task-latest-file/">{/if}{Localisation::getTranslation('common_complete')}{if !empty($allow_downloads[$task_id])}</a>{/if}
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                        <input type="hidden" name="totalTaskPreReqs" value="{$i}" />
                    </table>                            
                </td>
            </tr>
            {/if}

               
 
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}

           </tbody>

        </table>
        </div>


            <div class="d-flex justify-content-center mt-4 flex-wrap">
                <div>
                    <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='btn btn-danger text-white'>
                        <i class="icon-ban-circle icon-white"></i> {Localisation::getTranslation('common_cancel')}
                    </a>
                   
                </div>
                <div class="ms-4">
                   
                    <p>
                        <button type="submit" onclick="return validateForm();" value="Submit" name="submit" class="btn btn-primary text-white">
                            <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('task_alter_update_task_details')}
                        </button>
                    </p>    
                  
                </div>
            </div>     
     
       
    </form>


    </div>



</section>

</div>

{* <section class="ftco-section">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 mb-5 text-center">
            <h2 class="heading-section">Calendar #09</h2>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-4 text-center">
            <h3 class="h5">Calendar Datepicker</h3>
            <div class="w-100">
                <form action="" method="post" class="datepickers">
                    <div class="form-group">
                        <!-- <label class="label-control" for="id_start_datetime">Datetime picker</label> -->
                        <div class="input-group date" id="id_0">
                            <input
                                type="text"
                                value=""
                                class="form-control"
                                name="datePicker"
                                placeholder="MM/DD/YYYY hh:mm:ss"
                                required
                            />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</section> *}

   

                        
{include file="footer2.tpl"}
