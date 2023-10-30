{if isset($top_tags) AND is_array($top_tags) AND count($top_tags) > 0}
<div class="">
    <h3><i class="icon-tags"></i> {Localisation::getTranslation('tags_top_list_inc_popular_tags')}</h3>
    <div class="d-flex row wrap justify-content">
        
            {foreach $top_tags as $tag}
                <div class="tag col-6 p-2">
                    {assign var="tag_label" value=TemplateHelper::uiCleanseHTML($tag->getLabel())}
                    {assign var="tagId" value=$tag->getId()}
                     <div class="border border-secondary p-2 text-center "><a href="{urlFor name="tag-details" options="id.$tagId"}" class="link-underline-light">{$tag_label}</a></div>
                </div>            
            {/foreach}
           
        
    </div>
     <div class="tag">
                <a class="btn btn-primary btn-small" href="{urlFor name="tags-list"}"><i class="icon-list icon-white"></i> {Localisation::getTranslation('tags_top_list_inc_more_tags')}</a>
    </div>
</div>    
   
{/if}
