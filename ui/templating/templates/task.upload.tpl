{include file="header.tpl"}

<div class="page-header">
	<h1>Upload a document to be translated <small>Create a translation task</small></h1>
</div>

<div class="grid_8">
	{if isset($error)}
		<div class="alert alert-error">
			<h4>Error</h4>
			<p>{$error}</p>
		</div>
	{/if}
	<form class="well" method="post" action="{$url_task_upload}" enctype="multipart/form-data">
		<p><label for="{$field_name}">Choose your file</label>  
		<input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size_bytes}"/>
		<input type="file" name="{$field_name}" id="{$field_name}"/></p>
		<p class="help-block">Can be anything, even a .zip collection of files. Max file size {$max_file_size_mb}MB.</p>

		<input type="hidden" name="organisation_id" value="1"/>
		<input class="btn btn-success" type="submit" value="    Upload my selected file" name="submit"/>
                <i class="icon-upload icon-white" style="position:relative; right:170px; top:2px;"></i> 
	</form>
</div>
{include file="footer.tpl"}
