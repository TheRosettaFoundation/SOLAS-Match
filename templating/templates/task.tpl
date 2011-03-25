{include file="header.inc.tpl"}
	<div class="grid_8">
		<div class="task_content">
			<h2>{$task->title()}</h2>
	
			<p class="details">
				<span class="time_since">{$s->io->timeSince($task->createdTime())} ago</span> {$task->organisation()}
			</p>
			{assign var="tag_ids" value=$task->tagIDs()}
			{if $tag_ids}
				<ul class="tags">
					{foreach from=$tag_ids item=tag_id}
						<li>{$s->tags->tagHTML($tag_id)}</li>
					{/foreach}
				</ul>
			{/if}
	
			{if isset($task_files)}
				{foreach from=$task_files item=task_file}
					<h3>{$task_file->filename(0)}</h3>
					<ul>
						{assign var="times_downloaded" value=$task_file->timesDownloaded()}
						{if $times_downloaded == 1}
							<li><span class="time_since">Downloaded one time.</span></li>
						{else if $times_downloaded > 1}
							<li><span class="time_since">Downloaded {$times_downloaded} times.</span></li>
						{/if}
						{assign var="latest_version" value=$task_file->latestVersion()}
						<li><em>Volunteers:</em> <a href="{$task_file->url()}">Download the file to translate it.</a></li>
						{if $latest_version > 0}
							<li><em>NGO:</em> <a href="{$task_file->urlVersion($latest_version)}">Download the latest translation.</a></li>
						{/if}
					</ul>
					
					<form method="post" action="/process/upload.edited_file.php" enctype="multipart/form-data">
						{assign var='max_upload_file_size' value=$s->setting('files.max_upload_file_size')}
						<input type="hidden" name="task_id" value="{$task_file->taskID()}">
						<input type="hidden" name="file_id" value="{$task_file->fileID()}">
						<fieldset>
							<p><label for="edited_file">Upload translated file</label>  
							<input type="file" name="edited_file" id="edited_file"></p>
							<p class="desc">Can be anything, even a .zip collection of files. Max file size {round($max_upload_file_size/1024, 1)}MB.</p>  
							<input type="submit" value="Submit" name="submit">
						</fieldset> 
					</form>
			
				{/foreach}
			{/if}
		</div>
	</div>
	<div id="sidebar" class="grid_4">
		<p><a href="/mockup/pm.create-task.php">+ New task</a></p>
	</div>

{include file="footer.inc.tpl"}
