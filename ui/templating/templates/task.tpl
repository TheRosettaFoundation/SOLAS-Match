{include file="header.tpl"}
{assign var="task_id" value=$task->getId()}

{include file="handle-flash-messages.tpl"}

<div class="page-header">
	<h1>{$task->getTitle()} <small>Translation task details</small></h1>
</div>

	<p>
		{if $task->getSourceLangId()}
			From <b>{TemplateHelper::languageNameFromId($task->getSourceLangId())}</b>
		{/if}
		{if $task->getTargetLangId()}
			To <b>{TemplateHelper::languageNameFromId($task->getTargetLangId())}</b>
		{/if}
        <div class="tag">
		{foreach from=$task->getTags() item=tag}
			<a href="{urlFor name="tag-details" options="label.$tag"}" class="label">{$tag}</a>
		{/foreach}
        </div>
	</p>
		
	<p>
		<span class="time_since">{TemplateHelper::timeSinceSqlTime($task->getCreatedTime())} ago</span>

		&middot;
        {assign var="org_id" value=$task->getOrgId()}
        
        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
            {TemplateHelper::orgNameFromId($task->getOrgId())}
        </a>
		{assign var="wordcount" value=$task->getWordCount()}
		{if $wordcount}
			&middot; {$wordcount|number_format} words
		{/if}
	</p>
        <p style="margin-bottom:20px;"></p>
        <hr>
    <h3>{$org->getName()} <small>Organisation Information</small>
    <p style="margin-bottom:20px;"></p>
    {assign var="ref" value=''}
    {if $task->getReferencePage() != ''}
        {assign var="ref" value=$task->getReferencePage()}
        {assign var="button" value="Page Reference"}
    {elseif $org->getHomePage() != 'http://'}
        {assign var="ref" value=$org->getHomePage()}
        {assign var="button" value="Organisation Home Page"}
    {/if}
    {if $ref != ''}
        <a target="_blank" class="btn pull-right" href="{$ref}">{$button}</a>
    {/if}
    </h3>
    {if $org->getBiography() != ''}
        <p><b>About the Organisation:</b> {$org->getBiography()}</p>
    {/if}
    {if $task->getImpact() != ''}
        <p><b>Impact:</b> {$task->getImpact()}</p>
    {/if}

{if isset($file_previously_uploaded) && $file_previously_uploaded}
    <br />
    <div class="alert">
        <p>Thanks for providing your translation for this task. 
        {if $org != null && $org->getName() != ''}
            {$org->getName()}
        {else}
            This organisation
        {/if}
        will be able to use it for their benefit.</p>
        <p><strong>Warning! </strong>Uploading a new version of the file will overwrite the old one.</p>
    </div>
{/if}

{if !(isset($this_user_has_claimed_this_task))}
    {if isset($user)}
        <hr />
        <h3>Are you interested in volunteering to translate this task?</h3>
        <p> 
            <a href="{urlFor name="download-task-preview" options="task_id.$task_id"}" class="btn btn-large btn-primary">
            <i class="icon-download icon-white"></i> Download the file to preview</a>
        </p>
    {else}
        <div class="page-header">
            <h1>Participate in this task</h1>
        </div>
        <p>When you register, you'll be able to <strong>preview</strong> this task, and to translate it if you wish.</p>
        <p>
            <a class="btn btn-primary" href="{urlFor name="register"}">Register</a>
            <a class="btn" href="{urlFor name="login"}">Log In</a>
        </p>
     {/if}
     <hr />
     <center><h2>Document Preview <small>{$filename}</small></h2></center>
    <iframe src="http://docs.google.com/viewer?url={urlencode($file_preview_path)}&embedded=true" width="800" height="780" style="border: none;"></iframe>
{/if}
<p style="margin-bottom:40px;"></p>
{if isset($this_user_has_claimed_this_task)}
    <div class="page-header">
        <h1>Can't find the task file? <small>Misplaced the original file or the latest uploaded file?</h1>
    </div>
    <p>Click 
        <a href="{urlFor name="download-task" options="task_id.$task_id"}">here</a>
        to re-download the original task file.
    </p>  
    {* //todo When API function is available
    <p>Click 
        <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}">here</a>
        to re-download the latest updated task file.
    </p>   
    *}
    
    <p style="margin-bottom:40px;"></p>
    
	<div class="page-header">
		<h1>Finished translating? <small>{$filename}</small></h1>
	</div>
	{if isset($upload_error)}
		<div class="alert alert-error">
			<strong>Upload error</strong> {$upload_error}
		</div>
	{/if}
	<h3>Upload your translated version of {$filename}</h3>
	<form class="well" method="post" action="{urlFor name="task-upload-edited" options="task_id.$task_id"}" enctype="multipart/form-data">
		<input type="hidden" name="task_id" value="{$task->getId()}">
		<input type="file" name="edited_file" id="edited_file">
		<p class="help-block">
			Max file size {$max_file_size}MB.
		</p> 
		<button type="submit" value="Submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> Upload the file I chose</button>
	</form>
{else if isset($task_is_claimed)}
	<hr>
	<h3>Task has been claimed by a volunteer</h3>
	<p>Please continue to browse in search of open tasks.</p>	
{/if}

{include file="footer.tpl"}
