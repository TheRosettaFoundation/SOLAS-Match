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
                <option value= "{$NGO_ADMIN}"> ADMIN</option>
                <option value= "{$NGO_PROJECT_OFFICER}"> PROJECT OFFICER</option>
                <option value= "{$NGO_LINGUIST}"> LINGUISTIC </option>            
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
       
               {if ($sent) } 

           
             
                <h3> Sent Invitations </h3> </br>

                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Link</th>
                        </tr>
                    </thead>
                    <tbody>
                     {assign var=count value=0}
                     {foreach $sent  as $rec}
                        {assign var=number value=$count+1}
                        <tr>
                        <th scope="row">$number</th>

                        <td>{$rec.email }</td>
                        <td> 
                            {if ($rec.roles === 2 )} 
                                 LINGUISTIC 
                            {/if} 

                            {if ($rec.roles === 8 )} 
                                ADMIN 
                            {/if} 

                            {if ($rec.roles === 4 )} 
                                PROJECT OFFICER  
                            {/if} 
                                                
                        </td>

                        <td> <a href="/{$rec.url}" target="_blank" > {$rec.url}</a></td>
                        </tr>

                     {/foreach}
            

                    </tbody>
                    </table>    
                     
                      

                {/if}
         

        
        </div>
{include file="footer.tpl"}
