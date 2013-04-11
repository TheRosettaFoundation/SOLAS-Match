{include file="header.tpl"}
    <div class="grid_8">
        <div class="page-header">
            <h1>
                Create New Task <small>For Project: <strong>{$project->getTitle()}</strong></small><br>   
                <small>
                    Note:
                    <span style="color: red">*</span>
                    Denotes required field.
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
                                <h2>Title: <span style="color: red">*</span></h2>
                                <p class="desc">Provide a meaningful title for the task.</p>
                                {if !is_null($titleError)}
                                    <div class="alert alert-error" style="width:131px">
                                        {$titleError}
                                    </div>
                                {/if}
                            </label>
                            <textarea wrap="soft" cols="1" rows="3" name="title">{$task->getTitle()}</textarea>				
                            <p style="margin-bottom:20px;"/>

                            <label for="comment"><h2>Task Comment:</h2></label>
                            <p>Who and what will be affected by the translation of this task.</p>
                            <textarea wrap="soft" cols="1" rows="4" name="comment">{$task->getComment()}</textarea>
                            <p style="margin-bottom:20px;"/>

                            <p>
                                <h2>Source Language:</h2><br>
                                <p>
                                    {TemplateHelper::languageNameFromCode($project->getSourceLanguageCode())}
                                    ({TemplateHelper::countryNameFromCode($project->getSourceCountryCode())})
                                </p>
                            </p>
                            <p style="margin-bottom:20px;"/>
                            <p>
                                <h2>Target Language: <span style="color: red">*</span></h2><br>
                                <select name="targetLanguage" id="targetLanguage">
                                    {foreach $languages as $language}
                                        {if TemplateHelper::getLanguage($task->getTargetLocale()) == $language->getCode()}
                                            <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
                                        {else}
                                            <option value="{$language->getCode()}">{$language->getName()}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </p>
                            <p>
                                {if isset($countries)}
                                    <select name="targetCountry" id="targetCountry">
                                        {foreach $countries as $country}
                                            {if $task->getTargetCountryCode() == $country->getCode()}
                                                <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                                            {else}
                                                <option value="{$country->getCode()}">{$country->getName()}</option>
                                            {/if}
                                        {/foreach}
                                    </select> 
                                {/if}
                            </p>
                        </td>
                        <td>                            
                            <h2>Task Type: <span style="color: red">*</span></h2>
                            <p class="desc">Provide the type of the task.</p>
                            <select name="taskType">
                                {assign var="taskTypeCount" value=count($taskTypes)}
                                {for $id=1 to $taskTypeCount}
                                    <option value="{$id}">{$taskTypes[$id]}</option>
                                {/for}
                            </select>
                            <p style="margin-bottom:40px;"/>
                            <p>
                                <label for="word_count">
                                <h2>Word Count: <span style="color: red">*</span></h2>
                                <p class="desc">Approximate, or use a site such as <a href="http://wordcounttool.net/" target="_blank">Word Count Tool</a>.</p>
                                {if !is_null($wordCountError)}
                                    <div class="alert alert-error" style="width:144px">
                                        {$wordCountError}
                                    </div>
                                {/if}
                                </label>  
                                <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}"/>
                            </p>
                            <p style="margin-bottom:40px;"/>

                            <h2>Deadline: <span style="color: red">*</span></h2>
                            <p class="desc">Provide a deadline by which the task must be completed.</p>
                            {if $deadlineError != ''}
                                <div class="alert alert-error">
                                    {$deadlineError}
                                </div>
                            {/if}
                            <p>
                                {assign var="deadlineDateTime" value=$task->getDeadline()}
                                <input class="hasDatePicker" type="text" id="deadline" name="deadline" value="{if isset($deadlineDateTime)} {date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))} {/if}" />
                            </p>
                            <p style="margin-bottom:40px;"/>

                            <p>
                                <h2>Publish Task</h2>
                                <p class="desc">Do you want the task to be published on the live task steam.</p>
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
                                <h2>Task Prerequisite(s):</h2>
                                <p class="desc">Assign prerequisites for this task - if any.</p>
                                <p>
                                    These are tasks that must be completed before the current task becomes available.
                                </p>
                                <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all;" width="100%" >
                                    <thead>
                                        <th>Assign</th>
                                        <th>Title</th>
                                        <th>Source Language</th>
                                        <th>Target Language</th>
                                        <th>Type</th>
                                        <th>Status</th>
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
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">SEGMENTATION</span>                                    
                                                {elseif $type_id == TaskTypeEnum::TRANSLATION}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation</span> 
                                                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading</span> 
                                                {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                                                    <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">Desegmentation</span> 
                                                {/if}
                                            </td>
                                            <td>                                            
                                                {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                                                    Waiting
                                                {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                                                    Unclaimed
                                                {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                                                    <a href="{urlFor name="task-org-feedback" options="task_id.$task_id"}">In Progress</a>
                                                {elseif $status_id == TaskStatusEnum::COMPLETE}
                                                    <a href="{Settings::get("site.api")}v0/tasks/{$task_id}/file/?">Complete</a>
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
                                <i class="icon-ban-circle icon-white"></i> Cancel
                            </a>
                                <p style="margin-bottom:20px;"></p>  
                        </td>                    
                        <td>
                            <p style="margin-bottom:20px;"/>  
                            <button type="submit" value="Submit" name="submit" class="btn btn-success">
                                <i class="icon-upload icon-white"></i> Create Task
                            </button>
                            <p style="margin-bottom:20px;"/>  
                        </td>
                    </tr>                
                </fieldset>
            </table>
        </form>
    </div>
{include file="footer.tpl"}
