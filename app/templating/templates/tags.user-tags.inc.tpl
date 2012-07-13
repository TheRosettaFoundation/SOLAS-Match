{if isset($user_tags) AND is_array($user_tags)}
    <h3><i class="icon-tags"></i>Tags You Have Subscribed To</h3>
    <ul class="nav nav-list unstyled">
        {foreach from=$user_tags item=tag}
            <li>
                <div class="tag">
                    <a class="label" href="{urlFor name="tag-details" options="label.$tag"}">{$tag}</a>
                    <a class="label label-close" href="{urlFor name="home"}tag/{$tag}/false" title="Click to remove tag from subscription list"><b>| x</b></a>
                </div>
            </li>
        {/foreach}
    </ul>
{/if}
