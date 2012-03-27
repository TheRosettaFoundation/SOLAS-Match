{include file="header.tpl"}
<div class="page-header">
	<h1>{$task->getTitle()} <small>Translation task</small></h1>
</div>

{assign var="task_id" value=$task->getTaskId()}
{if isset($user)}
	<p>
		<a href="{urlFor name="download-task" options="task_id.$task_id"}" class="btn btn-large btn-primary">Download and translate</a>
	</p>
{/if}
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

{if isset($task_file_info)}
	{assign var="task_id" value=$task->getTaskId()}
	{if isset($user)}
		<hr>
		{if isset($upload_error)}
			<div class="alert alert-error">
				<strong>Upload error</strong> {$upload_error}
			</div>
		{/if}
		<h2>Finished translating? <small>Upload your changes</small></h2>
		<form class="well" method="post" action="{urlFor name="task-upload-edited" options="task_id.$task_id"}" enctype="multipart/form-data">
			<input type="hidden" name="task_id" value="{$task->getTaskId()}">
			<input type="file" name="edited_file" id="edited_file">
			<p class="help-block">
				Max file size {$max_file_size}MB.
			</p> 
			<button type="submit" value="Submit" name="submit" class="btn">Submit</button>
		</form>
	{else}
		<div class="page-header">
			<h1>Participate in this task</h1>
		</div>
		<p>
			<a class="btn btn-primary" href="{urlFor name="register"}">Register</a>
			<a class="btn" href="{urlFor name="login"}">Log In</a>
		</p>
	{/if}
{/if}

{include file="footer.tpl"}
