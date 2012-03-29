{if isset($top_tags) AND is_array($top_tags)}
	<h3><i class="icon-tags"></i> Popular tags</h3>
	<ul class="nav nav-list unstyled">
		{foreach from=$top_tags item=tag}
			<li>
				<a class="tag" href="{URL::tag($tag)}"><span class="label">{$tag}</span></a>
			</li>
		{/foreach}
	</ul>
{/if}
