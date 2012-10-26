{include file="header.tpl"}

{assign var="task_id" value=$task->getTaskId()}
<h1 class="page-header">
    Task {$task->getTitle()}
    <small>Alter task details here</small>
    <a href="{urlFor name="task-view" options="task_id.$task_id"}" class='pull-right btn btn-primary'>
        View Details
    </a>
</h1>

<h3>Edit Task Details</h3>
<p style="margin-bottom:20px;"></p>
<form method="post" action="{urlFor name="task-alter" options="task_id.$task_id"}" class="well">
    <label for="title">Title</label>
    <textarea wrap="hard" cols="1" rows="2" name="title">{$task->getTitle()}</textarea>

    <label for="impact">Task Impact</label>
    <textarea wrap="hard" cols="1" rows="2" name="impact">{$task->getImpact()}</textarea>

    <label for="reference">Context Reference</label>
    {if $task->getReferencePage() != '' }
        {assign var="url_text" value=$task->getReferencePage()}
    {else}
        {assign var="url_text" value="http://"}
    {/if}
    <textarea wrap="hard" cols="1" rows="2" name="reference">{$url_text}</textarea>

    <label for="source">Source Language</label>
        <select name="source" id="source">
            {foreach $languages as $language}
                {if $task->getSourceId() == $language[2]}
                    <option value="{$language[2]}" selected="selected">{$language[0]}</option>
                {else}
                    <option value="{$language[2]}">{$language[0]}</option>
                {/if}
            {/foreach}
        </select>
    {if isset($countries)}
        <select name="sourceCountry" id="sourceCountry">
            {foreach $countries as $country}
                {if $task->getSourceCountryCode() == $country['code']}
                    <option value="{$country['code']}" selected="selected">{$country['country']}</option>
                {else}
                    <option value="{$country['code']}">{$country['country']}</option>
                {/if}
            {/foreach}
        </select>
    {/if}

    <label for="target">Target Language</label>
    <select name="target" id="target">
        {foreach $languages as $language}
            {if $task->getTargetId() == $language[2]}
                <option value="{$language[2]}" selected="selected">{$language[0]}</option>
            {else}
                <option value="{$language[2]}">{$language[0]}</option>
            {/if}
        {/foreach}
    </select>
    {if isset($countries)}
        <select name="targetCountry" id="targetCountry">
            {foreach $countries as $country}
                {if $task->getTargetCountryCode() == $country['code']}
                    <option value="{$country['code']}" selected="selected">{$country['country']}</option>
                {else}
                    <option value="{$country['code']}">{$country['country']}</option>
                {/if}
            {/foreach}
        </select>
    {/if}

    <label for="tags">Tags</label>
    <input type="text" name="tags" id="tags" value="{$tag_list}">

    {if !is_null($word_count_err)}
        <div class="alert alert-error">
            {$word_count_err}
        </div>
    {/if} 
    
    <label for="word_count">Word Count</label>
    <input type="text" name="word_count" id="word_count" maxlength="6" value="{$task->getWordCount()}">

    <p>
        <button type="submit" value="Submit" name="submit" class="btn btn-primary"> Submit</button>
    </p>
</form>


{include file="footer.tpl"}
