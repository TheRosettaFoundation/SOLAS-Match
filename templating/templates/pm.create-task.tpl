{include file="header.inc.tpl"}
	<div class="grid_8">
		<h2>Create task</h2>
		<form method="POST" action="/process/pm.create-task.php">
			<fieldset>
				<label for="content">Title</label>
				<textarea wrap="hard" cols="1" rows="2" name="title"></textarea>
				<label for="tags">Tags &ndash; separated by spaces</label>
				<input type="text" name="tags" id="tags">
				<label for="organisation_id">Organisation</label>
				{assign var="organisation_ids" value=$s->orgs->organisationIDs()}
				{if $organisation_ids}
					<select name="organisation_id" id="organisation_id">
					{foreach from=$organisation_ids item=i}
						<option value="{$i}">{$s->orgs->name($i)}</option>
					{/foreach}
					</select>
				{/if}				
				<br><br>
				<input type="submit" value="Submit" name="submit">
			</fieldset> 
		</form>
	</div>
{include file="footer.inc.tpl"}
