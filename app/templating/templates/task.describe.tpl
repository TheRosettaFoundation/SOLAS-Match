{include file="header.tpl"}
	<div class="grid_8">
		<h2>Describe your task</h2>
		{if isset($error)}
			<p class="error">{$error}</p>
		{/if}
		<form method="post" action="{$url_task_describe}">
			<fieldset>
				<p>
					<label for="source">From language</label>
					<input type="text" name="source" id="source">
				</p>
				
				<p>
					<label for="target">To language</label>
					<input type="text" name="target" id="target">
				</p>
				
				<label for="content">Descriptive Title</label>
				<textarea wrap="hard" cols="1" rows="2" name="title"></textarea>
				
				<p>
					<label for="tags">Tags</label>
					<input type="text" name="tags" id="tags">
				</p>
				<p class="desc">Separated by spaces. For fultiword tags: join-with-hyphens</p>
				
				<p>
					<label for="word_count">Word count</label>  
					<input type="text" name="word_count" id="word_count" maxlength="6">
				</p>
				<p class="desc">Approximate, or leave black.</p>  
				
				<input type="submit" value="Save description" name="submit">
			</fieldset> 
		</form>
	</div>
{include file="footer.tpl"}
