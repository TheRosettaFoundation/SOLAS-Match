{if isset($top_tags) AND is_array($top_tags)}
	<h3>Popular tags</h3>
	<ul class="nav nav-list unstyled">
		{foreach from=$top_tags item=tag}
			<li>
				{include file="inc.tag.tpl" tag=$tag}
			</li>
		{/foreach}
	</ul>
{/if}
