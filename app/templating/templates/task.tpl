{include file="header.inc.tpl"}
	<div class="grid_8">
		<h2>{$task->getTitle()}</h2>
	
		<div class="task_content">
			
			<p class="details">
				<span class="time_since">{IO::timeSinceSqlTime($task->getCreatedTime())} ago</span>
				&middot; {Organisations::nameFromId($task->getOrganisationId())}
				{assign var="wordcount" value=$task->getWordCount()}
				{if $wordcount}
					&middot; {$wordcount|number_format} words
				{/if}
			</p>
			
			<ul class="tags">
				{if $task->areSourceAndTargetSet()}
					{Languages::languageNameFromId($task->getSourceId())} 
					to 
					{Languages::languageNameFromId($task->getTargetId())}
				{/if}
				{foreach from=$task->getTags() item=tag}
					<li>{include file="inc.tag.tpl" tag=$tag}</li>
				{/foreach}
			</ul>
		</div>

		{if isset($task_files)}
			{foreach from=$task_files item=task_file}
				{assign var="task_id" value=$task->getTaskId()}
				<h3>Task file: "{$task_file.filename}"</h3>
				<ul>
					{if isset($user)}
						<li><em>Volunteers:</em> <a href="{urlFor name="download-task" options="task_id.$task_id"}">Download the file to translate it.</a></li>
						<li><em>NGO:</em> <a href="{urlFor name="download-task-latest-version" options="task_id.$task_id"}">Download the latest translation.</a></li>
					{/if}
				</ul>

				{if isset($user)}
					<form method="post" action="{urlFor name="task-upload-edited" options="task_id.$task_id"}" enctype="multipart/form-data">
						<input type="hidden" name="task_id" value="{$task->getTaskId()}">
						<fieldset>
							<p>
								<label for="edited_file">Upload translated file</label>
								<input type="file" name="edited_file" id="edited_file">
							</p>
							<p class="desc">
								Can be anything, even a .zip collection of files. Max file size {$max_file_size}MB.
							</p>  
							<input type="submit" value="Submit" name="submit">
						</fieldset> 
					</form>
				{else}
					<p>Please <a href="{urlFor name="login"}">log in</a> to be able to accept translation jobs.</p>
				{/if}
			{/foreach}
		{/if}
	</div>
	<div id="sidebar" class="grid_4">
		<p><a href="{urlFor name="task-upload"}">+ New task</a></p>
	</div>



{include file="footer.inc.tpl"}
