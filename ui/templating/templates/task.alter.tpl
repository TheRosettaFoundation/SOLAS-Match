{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}
<h1 class="page-header">
    Task {$task->getTitle()}
    <small>Alter task details here.</small>
    <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
        <i class="icon-list icon-white"></i> View Task Details
    </a>
</h1>

<form method="post" action="{urlFor name="task-alter" options="task_id.$task_id"}" class="well">
    <table width="100%">
        <tr align="center">
            <td width="50%">
                <label for="title" style="font-size: large"><b>Title:</b></label>
                <textarea wrap="soft" cols="1" rows="4" name="title">{$task->getTitle()}</textarea>
                <p style="margin-bottom:20px;"></p>

                <label for="impact" style="font-size: large"><b>Task Comment:</b></label>
                <textarea wrap="soft" cols="1" rows="6" name="impact">{$task->getComment()}</textarea>
                <p style="margin-bottom:20px;"></p>

                <label for="deadline" style="font-size: large"><b>Deadline:</b></label>
                {if $deadline_error != ''}
                    <div class="alert alert-error">
                        {$deadline_error}
                    </div>
                {/if}
                <p>
                    {assign var="deadlineDateTime" value=$task->getDeadline()}
                    <input class="hasDatePicker" type="text" id="deadline" name="deadline" value="{if isset($deadlineDateTime)} {date(Settings::get("ui.date_format"), strtotime($task->getDeadline()))} {/if}" />
                </p>
            </td>
            <td>
                <p>
                    <label for="target" style="font-size: large"><b>Target Language:</b></label>
                    <select name="target" id="target">
                        {foreach $languages as $language}
                            {if $task->getTargetLanguageCode() == $language->getCode()}
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
                <p style="margin-bottom:40px;"></p>

                {if !is_null($word_count_err)}
                    <div class="alert alert-error">
                        {$word_count_err}
                    </div>
                {/if} 
                <p style="margin-bottom:40px;"></p>
                
                <label for="word_count" style="font-size: large"><b>Word Count:</b></label>
                <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}"/>
                <p style="margin-bottom:40px;"></p>

                <label for="prerequisites" style="font-size: large"><b>Task Prerequisites</b>:</label>
                {if isset($deadlockError) && $deadlockError != ''}
                    <p class="alert alert-error">{$deadlockError}</p>
                {/if}
                <p id="feedback">
                    <input type="hidden" name="selectedList" id="selectedList" value="{$hiddenPreReqList}" />
                    <span>You've selected:</span> <span id="select-result">none</span>.
                </p>
                <ol class ="pull-left" id="selectable">
                    {foreach $projectTasks as $projectTask}
                        {if $taskPreReqIds && in_array($projectTask->getId(), $taskPreReqIds)}
                            <li class="ui-widget-content ui-selected" value="{$projectTask->getId()}">{$projectTask->getTitle()}</li>
                        {else}
                            <li class="ui-widget-content" style="width: 470px" value="{$projectTask->getId()}">{$projectTask->getTitle()}</li>
                        {/if}
                    {/foreach}
                </ol>
            
            </td>             
        </tr>
        <tr>
            <td colspan="2">
                <hr/>
            </td>
        </tr>
        <tr align="center">
            <td>
                <p style="margin-bottom:20px;"></p>  
                <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='btn btn-danger'>
                    <i class="icon-ban-circle icon-white"></i> Cancel
                </a>
                <p style="margin-bottom:20px;"></p>  
            </td>
            <td>
                <p style="margin-bottom:20px;"></p>
                <p>
                    <button type="submit" value="Submit" name="submit" class="btn btn-primary">
                        <i class="icon-refresh icon-white"></i> Update Task Details
                    </button>
                </p>    
                <p style="margin-bottom:20px;"></p>
            </td>
        </tr>        
    </table>
</form>

{include file="footer.tpl"}
