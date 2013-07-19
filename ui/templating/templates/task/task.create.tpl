{include file="header.tpl"}
    <div class="grid_8">
        <div class="page-header">
            <h1>
                {Localisation::getTranslation(Strings::COMMON_CREATE_NEW_TASK)} <small>{Localisation::getTranslation(Strings::TASK_CREATE_FOR_PROJECT)} <strong>{$project->getTitle()}</strong></small><br>   
                <small>
                    {Localisation::getTranslation(Strings::COMMON_NOTE)}:
                    <span style="color: red">*</span>
                    {Localisation::getTranslation(Strings::COMMON_DENOTES_A_REQUIRED_FIELD)}.
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
        <form method="post" action="{urlFor name="task-create" options="project_id.$project_id"}" class="well">
            <table border="0" width="100%">
                <fieldset>
                    <tr align="center">
                        <td width="50%">
                            <label for="content">
                                <h2>{Localisation::getTranslation(Strings::COMMON_TITLE)}: <span style="color: red">*</span></h2>
                                <p class="desc">{Localisation::getTranslation(Strings::TASK_CREATE_0)}</p>
                                {if !is_null($titleError)}
                                    <div class="alert alert-error" style="width:131px">
                                        {$titleError}
                                    </div>
                                {/if}
                            </label>
                            <textarea wrap="soft" cols="1" rows="3" name="title" style="width: 400px">{$task->getTitle()}</textarea>				
                            <p style="margin-bottom:20px;"/>

                            <label for="comment"><h2>{Localisation::getTranslation(Strings::COMMON_TASK_COMMENT)}:</h2></label>
                            <p>{Localisation::getTranslation(Strings::TASK_CREATE_1)}</p>
                            <textarea wrap="soft" cols="1" rows="4" name="comment" style="width: 400px">{$task->getComment()}</textarea>
                            <p style="margin-bottom:20px;"/>

                            <p>
                                <h2>{Localisation::getTranslation(Strings::COMMON_SOURCE_LANGUAGE)}:</h2><br>
                                <p>
                                    {TemplateHelper::getLanguage($project->getSourceLocale())}
                                    - {TemplateHelper::getCountry($project->getSourceLocale())}
                                </p>
                            </p>
                            <p style="margin-bottom:20px;"/>
                            <p>
                                <h2>{Localisation::getTranslation(Strings::COMMON_TARGET_LANGUAGE)}: <span style="color: red">*</span></h2><br>
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
                            <h2>{Localisation::getTranslation(Strings::COMMON_TASK_TYPE)}: <span style="color: red">*</span></h2>
                            <p class="desc">{Localisation::getTranslation(Strings::TASK_CREATE_2)}</p>
                            <select name="taskType" style="width: 400px">
                                {assign var="taskTypeCount" value=count($taskTypes)}
                                {for $id=1 to $taskTypeCount}
                                    <option value="{$id}">{$taskTypes[$id]}</option>
                                {/for}
                            </select>
                            <p style="margin-bottom:40px;"/>
                            <p>
                                <label for="word_count">
                                <h2>{Localisation::getTranslation(Strings::COMMON_WORD_COUNT)}: <span style="color: red">*</span></h2>
                                <p class="desc">{Localisation::getTranslation(Strings::COMMON_APPROXIMATE_OR_USE_A_WEBSITE_SUCH_AS)} <a href="http://wordcounttool.net/" target="_blank">{Localisation::getTranslation(Strings::TASK_CREATE_WORD_COUNT_TOOL)}</a>.</p>
                                {if !is_null($wordCountError)}
                                    <div class="alert alert-error" style="width:144px">
                                        {$wordCountError}
                                    </div>
                                {/if}
                                </label>  
                                <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}" style="width: 400px"/>
                            </p>
                            <p style="margin-bottom:40px;"/>

                            <h2>{Localisation::getTranslation(Strings::COMMON_DEADLINE)}: <span style="color: red">*</span></h2>
                            <p class="desc">{Localisation::getTranslation(Strings::TASK_CREATE_3)}</p>
                            {if $deadlineError != ''}
                                <div class="alert alert-error">
                                    {$deadlineError}
                                </div>
                            {/if}
                            <p>
                                {assign var="deadlineDateTime" value=$task->getDeadline()}
                                <input class="hasDatePicker" type="text" id="deadline" name="deadline" value="{if isset($deadlineDateTime)} {date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))} {/if}" style="width: 400px" />
                            </p>
                            <p style="margin-bottom:40px;"/>

                            <p>
                                <h2>{Localisation::getTranslation(Strings::COMMON_PUBLISH_TASK)}</h2>
                                <p class="desc">{Localisation::getTranslation(Strings::TASK_CREATE_4)}</p>
                                <input type="checkbox" name="published" checked="true" />
                            </p>
                                              
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
                                <h2>{Localisation::getTranslation(Strings::COMMON_TASK_PREREQUISITES)}:</h2>
                                <p class="desc">{Localisation::getTranslation(Strings::COMMON_ASSIGN_PREREQUISITES_FOR_THIS_TASK_IF_ANY)}</p>
                                <p>
                                    {Localisation::getTranslation(Strings::COMMON_THESE_ARE_TASKS_THAT_MUST_BE_COMPLETED_BEFORE_THE_CURRENT_TASK_BECOMES_AVAILABLE)}
                                </p>
                                <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all;" width="100%" >
                                    <thead>
                                        <th>{Localisation::getTranslation(Strings::COMMON_ASSIGN)}</th>
                                        <th>{Localisation::getTranslation(Strings::COMMON_TITLE)}</th>
                                        <th>{Localisation::getTranslation(Strings::COMMON_SOURCE_LANGUAGE)}</th>
                                        <th>{Localisation::getTranslation(Strings::COMMON_TARGET_LANGUAGE)}</th>
                                        <th>{Localisation::getTranslation(Strings::COMMON_TYPE)}</th>
                                        <th>{Localisation::getTranslation(Strings::COMMON_STATUS)}</th>
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
                                                <a href="{urlFor name="task-view" options="task_id.$task_id"}">{$projectTask->getTitle()}</a>
                                            </td>
                                            <td>{TemplateHelper::getTaskSourceLanguage($projectTask)}</td>  
                                            <td>{TemplateHelper::getTaskTargetLanguage($projectTask)}</td>
                                            <td>                                            
                                                {if $type_id == TaskTypeEnum::SEGMENTATION}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_SEGMENTATION)}</span>
                                                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation(Strings::COMMON_TRANSLATION)}</span>
                                                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation(Strings::COMMON_PROOFREADING)}</span>
                                                {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_DESEGMENTATION)}</span>
                                                {/if}
                                            </td>
                                            <td>                                            
                                                {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                                    {Localisation::getTranslation(Strings::COMMON_WAITING)}
                                                {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                                    {Localisation::getTranslation(Strings::COMMON_UNCLAIMED)}
                                                {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                                    <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">{Localisation::getTranslation(Strings::COMMON_IN_PROGRESS)}</a>
                                                {elseif $status_id == TaskStatusEnum::COMPLETE}
                                                    <a href="{Settings::get("site.api")}v0/tasks/{$task_id}/file/?">{Localisation::getTranslation(Strings::COMMON_COMPLETE)}</a>
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
                                <i class="icon-ban-circle icon-white"></i> {Localisation::getTranslation(Strings::COMMON_CANCEL)}
                            </a>
                                <p style="margin-bottom:20px;"></p> 
                        </td>                    
                        <td>
                            <p style="margin-bottom:20px;"/>  
                            <button type="submit" value="Submit" name="submit" class="btn btn-success">
                                <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::COMMON_CREATE_TASK)}
                            </button>
                            <p style="margin-bottom:20px;"/>
                        </td>
                    </tr>                
                </fieldset>
            </table>
        </form>
    </div>
{include file="footer.tpl"}
