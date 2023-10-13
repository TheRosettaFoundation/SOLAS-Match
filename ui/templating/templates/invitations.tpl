 
        <div id ="registrations-id">
       
               {if ($sent) } 
             
                <h3 class="mt-5"> Sent Invitations </h3> <br />

                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Used</th>
                        <th scope="col">Expires at</th>
                        <th scope="col">Link</th>

                        </tr>
                    </thead>
                    <tbody>
                     {foreach $sent  as $rec}
                     
                        <tr>                     
                            <td> <a href="mailto:{$rec.email}?subject={rawurlencode('TWB Registration')}" target="_blank">{$rec.email}</a></td>
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

                                {if ($rec.roles === 16 )} 
                                    COMMUNITY OFFICER  
                                {/if}                                                   
                            </td>
                            <td>
                                 {if ($rec.used === 0 )} 
                                 
                                 {/if}    

                                  {if ($rec.used > 0 )} 
                                     Yes
                                 {/if}                               
                            </td>
                            <td>
                                {$rec.date_expires}
                            </td>
                            <td> <a href="{Settings::get('site.location')}{$rec.url}" target="_blank" >{Settings::get('site.location')}{$rec.url}</a></td>
                        </tr>

                     {/foreach}
                    </tbody>
                    </table>    
                     
                {/if}
                 
        </div>