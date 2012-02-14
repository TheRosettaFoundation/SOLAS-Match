{include file="header.inc.tpl"}
	<h1>Upload a document to be translated</h1>
	<div class="grid_8">
		{if isset($error)}
			<p class="error">{$error}</p>
		{/if}
		<form method="post" action="{$url_task_upload}" enctype="multipart/form-data">
			<fieldset>
				<p><label for="original_file">Upload your file of any format</label>  
				<input type="file" name="{$form_file_field}" id="{$form_file_field}"></p>
				<p class="desc">Can be anything, even a .zip collection of files. Max file size {$max_file_size_mb}MB.</p>
				
				<input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size_bytes}" />
				<input type="hidden" name="organisation_id" value="1">
				<input type="submit" value="Submit" name="submit">
			</fieldset> 
		</form>
	</div>
{include file="footer.inc.tpl"}
