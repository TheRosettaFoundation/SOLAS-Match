{include file="header.inc.tpl"}
	<div class="grid_8">
		<h2>Create task</h2>
		<form method="post" action="/process/pm.create-task.php" enctype="multipart/form-data">
			<fieldset>
				<label for="content">Descriptive Title</label>
				<textarea wrap="hard" cols="1" rows="2" name="title"></textarea>
				
				<p><label for="file">File to be translated</label>  
				<input type="file" name="file" id="file"></p>
				<p class="desc">Can be anything, even a .zip collection of files.</p>  
						
				<p><label for="tags">Tags</label>
				<input type="text" name="tags" id="tags"></p>
				<p class="desc">Separated by spaces</p>  
				
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
