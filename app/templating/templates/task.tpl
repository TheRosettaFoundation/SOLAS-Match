{include file="header.tpl"}
{assign var="task_id" value=$task->getTaskId()}

<div class="page-header">
	<h1>{$task->getTitle()} <small>Translation task</small></h1>
</div>

<section>
	<p>
		{if $task->getSourceId()}
			From {Languages::languageNameFromId($task->getSourceId())}
		{/if}
		{if $task->getTargetId()}
			To {Languages::languageNameFromId($task->getTargetId())}
		{/if}
		{foreach from=$task->getTags() item=tag}
			<a class="tag" href="{URL::tag($tag)}"><span class="label">{$tag}</span></a>
		{/foreach}
	</p>
		
	<p>
		<span class="time_since">{IO::timeSinceSqlTime($task->getCreatedTime())} ago</span>
		&middot; {Organisations::nameFromId($task->getOrganisationId())}
		{assign var="wordcount" value=$task->getWordCount()}
		{if $wordcount}
			&middot; {$wordcount|number_format} words
		{/if}
	</p>
</section>

{if isset($user_has_claimed_this_task)}
	<div class="page-header">
		<h1>Finished translating? <small>{$task_file_info.filename}</small></h1>
	</div>
	{if isset($upload_error)}
		<div class="alert alert-error">
			<strong>Upload error</strong> {$upload_error}
		</div>
	{/if}
	<h3>Upload your translated version of {$task_file_info.filename}</h3>
	<form class="well" method="post" action="{urlFor name="task-upload-edited" options="task_id.$task_id"}" enctype="multipart/form-data">
		<input type="hidden" name="task_id" value="{$task->getTaskId()}">
		<input type="file" name="edited_file" id="edited_file">
		<p class="help-block">
			Max file size {$max_file_size}MB.
		</p> 
		<button type="submit" value="Submit" name="submit" class="btn btn-primary"><i class="icon-upload"></i> Upload the file I chose</button>
	</form>
{else if isset($user)}
	<hr>
	<h3>Are you interested in volunteering to translate this task?</h3>
	<p>
		<a href="{urlFor name="download-task-preview" options="task_id.$task_id"}" class="btn btn-large btn-primary"><i class="icon-download icon-white"></i> Download the file to preview</a>
	</p>
{else}
	<div class="page-header">
		<h1>Participate in this task</h1>
	</div>
	<p>
		<a class="btn btn-primary" href="{urlFor name="register"}">Register</a>
		<a class="btn" href="{urlFor name="login"}">Log In</a>
	</p>
{/if}

{include file="footer.tpl"}
