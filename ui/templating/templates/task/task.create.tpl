{include file="header.tpl"}
    <div class="grid_8">
        <div class="page-header">
            <h1>
                {Localisation::getTranslation('common_create_new_task')} <small>{sprintf(Localisation::getTranslation('task_create_for_project'), {TemplateHelper::uiCleanseHTMLNewlineAndTabs($project->getTitle())})}</small><br>   
                <small>
                    {Localisation::getTranslation('common_denotes_a_required_field')}
                </small>
            </h1>
        </div>           

        {if isset($error) || isset($upload_error)}
            <div class="alert alert-error">
                {if isset($error)}
                    {$error}
                {else if isset($upload_error)}
                    {$upload_error}
                {/if}
            </div>
        {/if}

        {assign var="project_id" value=$project->getId()}
        <form method="post" action="{urlFor name="task-create" options="project_id.$project_id"}" class="well" accept-charset="utf-8">
            <table border="0" width="100%">
                <fieldset>
                    <tr align="center">
                        <td width="50%">
                            <label for="content">
                                <h2>{Localisation::getTranslation('common_title')} <span style="color: red">*</span></h2>
                                <p class="desc">{Localisation::getTranslation('task_create_0')}</p>
                                {if !is_null($titleError)}
                                    <div class="alert alert-error" style="width:131px">
                                        {$titleError}
                                    </div>
                                {/if}
                            </label>
                            <textarea wrap="soft" cols="1" rows="3" name="title" style="width: 400px">{$task->getTitle()|escape:'html':'UTF-8'}</textarea>        
                            <p style="margin-bottom:20px;"/>

                            <label for="comment"><h2>{Localisation::getTranslation('common_task_comment')}</h2></label>
                            <p>{Localisation::getTranslation('task_create_1')}</p>
                            <textarea wrap="soft" cols="1" rows="4" name="comment" style="width: 400px">{$task->getComment()|escape:'html':'UTF-8'}</textarea>
                            <p style="margin-bottom:20px;"/>

                            <p>
                                <h2>{Localisation::getTranslation('common_source_language')}</h2><br>
                                <p>
                                    {TemplateHelper::getLanguage($project->getSourceLocale())}
                                    - {TemplateHelper::getCountry($project->getSourceLocale())}
                                </p>
                            </p>
                            <p style="margin-bottom:20px;"/>
                            <p>
                                <h2>{Localisation::getTranslation('common_target_language')} <span style="color: red">*</span></h2><br>
                                <select name="targetLanguage" id="targetLanguage" style="width: 400px">
                                    {if $task->hasTargetLocale()}
                                        {assign var="languageCode" value=$task->getTargetLocale()->getLanguageCode()}
                                    {else}
                                        {assign var="languageCode" value=""}
                                    {/if}
                                    {foreach $languages as $language}
                                        <option value="{$language->getCode()}"
                                                {if $language->getCode() == $languageCode} selected="true" {/if}>
                                            {$language->getName()}
                                        </option>
                                    {/foreach}
                                </select>
                            </p>
                            <p>
                                {if isset($countries)}
                                    <select name="targetCountry" id="targetCountry" style="width: 400px">
                                        {if $task->hasTargetLocale()}
                                            {assign var="countryCode" value=$task->getTargetLocale()->getCountryCode()}
                                        {else}
                                            {assign var="countryCode" value=""}
                                        {/if}
                                        {foreach $countries as $country}
                                            <option value="{$country->getCode()}"
                                                    {if $country->getCode() == $countryCode} selected="true" {/if}>
                                                {$country->getName()}
                                            </option>
                                        {/foreach}
                                    </select> 
                                {/if}
                            </p>
                        </td>
                        <td>                            
                            <h2>{Localisation::getTranslation('common_task_type')} <span style="color: red">*</span></h2>
                            <p class="desc">{Localisation::getTranslation('task_create_2')}</p>
                            <select name="taskType" style="width: 400px">
                                <option value="2">Translation</option>
                                <option value="3">Proofreading</option>
                            </select>
                            <p style="margin-bottom:40px;"/>
                            <p>
                                <label for="word_count">
                                <h2>{Localisation::getTranslation('common_word_count')} <span style="color: red">*</span></h2>
                                <p class="desc">{Localisation::getTranslation('common_approximate_or_use_a_website_such_as')} <a href="http://wordcounttool.net/" target="_blank">{Localisation::getTranslation('task_create_word_count_tool')}</a>.</p>
                                {if !is_null($wordCountError)}
                                    <div class="alert alert-error" style="width:144px">
                                        {$wordCountError}
                                    </div>
                                {/if}
                                </label>  
                                <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}" style="width: 400px"/>
                            </p>
                            <p style="margin-bottom:40px;"/>

                            <h2>{Localisation::getTranslation('common_deadline')} <span style="color: red">*</span></h2>
                            <p class="desc">{Localisation::getTranslation('task_create_3')}</p>
                            {if $deadlineError != ''}
                                <div class="alert alert-error">
                                    {$deadlineError}
                                </div>
                            {/if}
                            <p>
                                {assign var="deadlineDateTime" value=$task->getDeadline()}
                                <input class="hasDatePicker" type="text" id="deadline_field" name="deadline_field" value="{if isset($deadlineDateTime)}{$task->getDeadline()}{/if}" style="width: 400px" />
                                <input type="hidden" name="deadline" id="deadline" />
                            </p>
                            <p style="margin-bottom:40px;"/>

                            <p>
                                <h2>{Localisation::getTranslation('common_publish_task')}</h2>
                                <p class="desc">{Localisation::getTranslation('task_create_4')}</p>
                                <input type="checkbox" name="published" checked="true" />
                            </p>

                            {if $showRestrictTask}
                            <p>
                                <h2>{Localisation::getTranslation('restrict_task')}</h2>
                                <p class="desc">{Localisation::getTranslation('restrict_task_long')}</p>
                                <input type="checkbox" name="restrictTask" checked="true" />
                            </p>
                           {/if}

                        </td>
                    </tr>
                    {if !is_null($projectTasks)}
                        <tr>
                            <td colspan="2">
                                <hr/>
                            </td> 
                        </tr>
                        <tr>
                            <td colspan="2">
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
                                                <input type="checkbox" name="preReq_{$i}" value="{$task_id}"/>
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
                                                    <a href="{Settings::get("site.api")}v0/tasks/{$task_id}/file/?">{Localisation::getTranslation('common_complete')}</a>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/foreach}
                                    <input type="hidden" name="totalTaskPreReqs" value="{$i}" />
                                </table>                            
                            </td>
                        </tr>
                    {/if}
                    <tr>
                        <td colspan="2">
                            <hr/>
                        </td> 
                    </tr>                    
                    <tr align="center">
                        <td>
                            <p style="margin-bottom:20px;"/>  
                            <a href='{urlFor name="project-view" options="project_id.$project_id"}' class='btn btn-danger'>
                                <i class="icon-ban-circle icon-white"></i> {Localisation::getTranslation('common_cancel')}
                            </a>
                                <p style="margin-bottom:20px;"></p> 
                        </td>                    
                        <td>
                            <p style="margin-bottom:20px;"/>  
                            <button type="submit" onclick="return validateForm();" value="Submit" name="submit" class="btn btn-success">
                                <i class="icon-upload icon-white"></i> {Localisation::getTranslation('common_create_task')}
                            </button>
                            <p style="margin-bottom:20px;"/>
                        </td>
                    </tr>                
                </fieldset>
            </table>
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
        </form>
    </div>
{include file="footer.tpl"}
