{assign var="top_tags" value=$s->tags->topTags(30)}
{if isset($top_tags)}
	<ul class="tags">
		{foreach from=$top_tags item=tag_freq}
			<li>{$s->tags->tagHTML($tag_freq.tag_id)}
				{if $tag_freq.frequency > 1}
					<span class="tag-frequency">x {$tag_freq.frequency}</span>
				{/if}
			</li>
		{/foreach}
	</ul>
{/if}
