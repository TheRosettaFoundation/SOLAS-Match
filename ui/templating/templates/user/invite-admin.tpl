{include file="header.tpl"}

    <div class="page-header">
            <h1> Assign Role {Settings::get('site.name')}</h1>
    </div>

    {if isset($flash['error'])}
        <div class="alert alert-error">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        </div>
    {/if}

    {if isset($flash['info'])}
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>{Localisation::getTranslation('common_note')}: </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
        </div>
    {/if}

    {if isset($flash['success'])}
        <div class="alert alert-success">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}</p>
        </div>
    {/if}
<div class="row-fluid">
        
        <form method="post" action="invite_admins" accept-charset="utf-8">
            <label for="role"> <strong> Select Role </strong> </>
            <select name ="role">
                <option value= "{$NGO_ADMIN}"> NGO ADMIN</option>
                <option value= "{$NGO_PROJECT_OFFICER}"> NGO PO</option>
                <option value= "{$NGO_LINGUIST}">NGO Linguist </option>            
             <select />

            <label for="email"><strong>{Localisation::getTranslation('common_email')}</strong></label>
            <input type="text" name="email" id="email"/>
            
            <div>
                <button type="submit" name="change-role" class="btn btn-primary">
  				    <i class="icon-share icon-white"></i> Submit
				</button>
				
				
            </div>
        </form>
          
       

   
</div>

        <div id ="registrations-id">
       
            {if ($records)}
             
                <h2> History</h2>

                    {foreach $records  as $rec}
                     
                       <p>   
                       <span> <b> Email</b> : {$rec.email } | </span>  
                       {if ($rec.roles === 8 ) } 
                            <span> <b> Role</b> : NGO ADMIN | </span>
                       {/if} 

                        {if ($rec.roles === 2 ) } 
                            <span> <b> Role</b> : TEST | </span>
                       {/if} 
                                        
                         {if ($rec.used === 0 ) } 
                            <span> <b> Not Used </b> </span>
                       {/if} 
                       <span><b>Link</b>: <a href = "/{$rec.url}"> {$rec.url }</a>   </span> 

                       
                                                       
                        </p>
                    {/foreach}
            {/if}

        
        </div>
{include file="footer.tpl"}
