{include file="header.inc.tpl"}
	<div class="grid_8">
		<h2>Create task</h2>
		<form method="post" action="/process/pm.create-task.php" enctype="multipart/form-data">
			{assign var='max_upload_file_size' value=$s->setting('files.max_upload_file_size')}
			<fieldset>
				<label for="content">Descriptive Title</label>
				<textarea wrap="hard" cols="1" rows="2" name="title"></textarea>
				
				<p><label for="original_file">File to be translated</label>  
				<input type="file" name="original_file" id="original_file"></p>
				<p class="desc">Can be anything, even a .zip collection of files. Max file size {round($max_upload_file_size/1024, 1)}MB.</p>  
						
				<p><label for="word_count">Word count</label>  
				<input type="text" name="word_count" id="word_count" maxlength="6"></p>
				<p class="desc">Approximate if needed, or just leave blank.</p>  
						
				<p><label for="tags">Tags</label>
				<input type="text" name="tags" id="tags"></p>
				<p class="desc">Separated by spaces.</p>  
				
				<p><label for="organisation_id">Organisation</label>
				{assign var="organisation_ids" value=$s->orgs->organisationIDs()}
				{if $organisation_ids}
					<select name="organisation_id" id="organisation_id">
					{foreach from=$organisation_ids item=i}
						<option value="{$i}">{$s->orgs->name($i)}</option>
					{/foreach}
					</select>
				{/if}</p>				
				
				<input type="submit" value="Submit" name="submit">
			</fieldset> 
		</form>
	</div>
{include file="footer.inc.tpl"}
