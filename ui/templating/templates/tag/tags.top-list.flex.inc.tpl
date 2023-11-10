{if isset($top_tags) AND is_array($top_tags) AND count($top_tags) > 0}
<div class="">
    <h3>{Localisation::getTranslation('tags_top_list_inc_popular_tags')}</h3>
    <div class="d-flex row justify-content-between ">
        
            {foreach $top_tags as $tag}
                <div class="tag col-6 p-2 text-wrap">
                    {assign var="tag_label" value=TemplateHelper::uiCleanseHTML($tag->getLabel())}
                    {assign var="tagId" value=$tag->getId()}
                     <div ><a href="{urlFor name="tag-details" options="id.$tagId"}" class="text-center p-1 rounded-5 text-wrap ">{$tag_label}</a></div>
                </div>            
            {/foreach}
           
        
    </div>
     <div class="mt-3 ">
                <a class="btn btn-primary d-block" href="{urlFor name="tags-list"}"> <span class="text-white"> {Localisation::getTranslation('tags_top_list_inc_more_tags')} </span></a>
    </div>
</div>    
   
{/if}
