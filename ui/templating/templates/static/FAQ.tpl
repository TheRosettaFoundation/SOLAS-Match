{include file="header.tpl"}

<div>    
    <div id="title" style="margin-top: 20px; text-align: center">
        <h1>{Localisation::getTranslation('faq_faq')}</h1>
    </div>
    
    <div id="quicklinks" style="margin-top: 20px;text-align: center"   >
        <h2>{Localisation::getTranslation('faq_quicklinks')}</h2>
        <h3>
            <a href="#volunteerQuestions">
                {Localisation::getTranslation('faq_volunteers')}
            </a>
            <span style="white-space: pre">      |    </span>
            <a href="#orgQuestions">
                {Localisation::getTranslation('common_organisation')}
            </a>
            <span style="white-space: pre">      |    </span>
            <a href="#faqQuestions">
                {Localisation::getTranslation('faq_terms')}
            </a>
        </h3>
    </div>
    
    <div style="margin-top: 25px;text-align: justify" class="well">
    
        <div id="volunteerQuestions">

            <h2>{Localisation::getTranslation('faq_volunteer_questions')}</h2>
            <hr />

            <p><a href="#vol1">{Localisation::getTranslation('faq_v_q1')}</a></p>
            <p><a href="#vol2">{Localisation::getTranslation('faq_v_q2')}</a></p>
            <p><a href="#vol3">{Localisation::getTranslation('faq_v_q3')}</a></p>
            <p><a href="#vol4">{Localisation::getTranslation('faq_v_q4')}</a></p>
            <p><a href="#vol5">{Localisation::getTranslation('faq_v_q5')}</a></p>
            <p><a href="#vol6">{sprintf(Localisation::getTranslation('faq_v_q6'), {Settings::get("site.name")})}</a></p>
            <p><a href="#vol7">{Localisation::getTranslation('faq_v_q7')}</a></p>   
            <p><a href="#vol8">{Localisation::getTranslation('faq_27')}</a></p> 
            <p><a href="#vol9">{Localisation::getTranslation('faq_31')}</a></p> 
            <p><a href="#vol10">{Localisation::getTranslation('faq_33')}</a></p>
            <p><a href="#vol11">{Localisation::getTranslation('faq_75')}</a></p>
            
            <hr />
        </div>

        <div id="volunteerAnswers">
            <h3 id="vol1">{Localisation::getTranslation('faq_v_q1')}</h3>
            <p>{Localisation::getTranslation('faq_v_a1')}</p>

            <h3 id="vol2">{Localisation::getTranslation('faq_v_q2')}</h3>
            <p>{sprintf(Localisation::getTranslation('faq_v_a2'), {Settings::get("site.system_email_address")}, {Settings::get("site.name")})}</p>

            <h3 id="vol3">{Localisation::getTranslation('faq_v_q3')}</h3>
            <p>{sprintf(Localisation::getTranslation('faq_v_a3'), {Settings::get("site.system_email_address")})}</p>

            <h3 id="vol4">{Localisation::getTranslation('faq_v_q4')}</h3>
            <p>{Localisation::getTranslation('faq_v_a4')}</p>

            <h3 id="vol5">{Localisation::getTranslation('faq_v_q5')}</h3>
            <p>{sprintf(Localisation::getTranslation('faq_v_a5'), {Settings::get("site.system_email_address")})}</p>

            <h3 id="vol6">{sprintf(Localisation::getTranslation('faq_v_q6'), {Settings::get("site.name")})}</h3>
            <p>{sprintf(Localisation::getTranslation('faq_v_a6'), {Settings::get("site.name")})}</p>

            <h3 id="vol7">{Localisation::getTranslation('faq_v_q7')}</h3>
            <p>{Localisation::getTranslation('faq_v_a7')}</p>

            <ol>
                <li>{sprintf(Localisation::getTranslation('faq_v_a7_all'), {Settings::get("site.name")})}</li>
                <li>{Localisation::getTranslation('faq_v_a7_strict')}</li>
            </ol>

            <p>{Localisation::getTranslation('faq_v_a7_2')}</p>

            <h3 id="vol8">{Localisation::getTranslation('faq_27')}</h3>
            <p>
            {Localisation::getTranslation('faq_28')} 
            {Localisation::getTranslation('faq_29')} 
            {Localisation::getTranslation('faq_30')}
            </p>

            <h3 id="vol9">{Localisation::getTranslation('faq_31')}</h3>
            <p>
            {Localisation::getTranslation('faq_32')}
            </p>

            <h3 id="vol10">{Localisation::getTranslation('faq_33')}</h3>
            <p>
            {Localisation::getTranslation('faq_34')} 
            {Localisation::getTranslation('faq_35')} 
            {Localisation::getTranslation('faq_36')}
            </p>
            
            <h3 id="vol11">{Localisation::getTranslation('faq_75')}</h3>
            <p>
            {Localisation::getTranslation('faq_76')} 
            {Localisation::getTranslation('faq_77')} 
            </p>

        </div>
    </div>   
            
            
 <div style="margin-top: 20px;text-align: justify" class="well">
    <div id="orgQuestions">
        <h2>{Localisation::getTranslation('faq_organisation_questions')}</h2>
        <hr />
        <p><a href="#org1">{Localisation::getTranslation('faq_o_q1')}</a></p>
        <p><a href="#org2">{Localisation::getTranslation('faq_o_q2')}</a></p>
        <p><a href="#org3">{Localisation::getTranslation('faq_o_q3')}</a></p>
        <p><a href="#org4">{Localisation::getTranslation('faq_o_q4')}</a></p>
        <p><a href="#org5">{Localisation::getTranslation('faq_o_q5')}</a></p>
        <p><a href="#org6">{Localisation::getTranslation('faq_59')}</a></p>
        <p><a href="#org7">{Localisation::getTranslation('faq_65')}</a></p>
        <hr />
    </div>
    
    <div id="orgAnswers">
        <h3 id="org1">{Localisation::getTranslation('faq_o_q1')}</h3>
        <p>{Localisation::getTranslation('faq_o_a1')}</p>
        
        <h3 id="org2">{Localisation::getTranslation('faq_o_q2')}</h3>
        <p>{Localisation::getTranslation('faq_o_a2')}</p>
        
        <h3 id="org3">{Localisation::getTranslation('faq_o_q3')}</h3>
        <p>{Localisation::getTranslation('faq_o_a3')}</p>
        
        <h3 id="org4">{Localisation::getTranslation('faq_o_q4')}</h3>
        <p>{sprintf(Localisation::getTranslation('faq_o_a4'), {Settings::get("site.name")})}</p>
        
        <h3 id="org5">{Localisation::getTranslation('faq_o_q5')}</h3>
        <p>{Localisation::getTranslation('faq_o_a5')}</p>
	
    	<h3 id="org6">{Localisation::getTranslation('faq_59')}</h3>
        <p>
            {Localisation::getTranslation('faq_60')} 
            {Localisation::getTranslation('faq_61')} 
            {Localisation::getTranslation('faq_62')} 
            {Localisation::getTranslation('faq_63')} 
            {Localisation::getTranslation('faq_64')}
        </p>
      
    	<h3 id="org7">{Localisation::getTranslation('faq_65')}</h3>
        <p>
            {Localisation::getTranslation('faq_66')} 
            {Localisation::getTranslation('faq_67')} 
            {Localisation::getTranslation('faq_68')} 
            {Localisation::getTranslation('faq_69')} 
            {Localisation::getTranslation('faq_70')} 
            {Localisation::getTranslation('faq_71')} 
            {Localisation::getTranslation('faq_72')} 
            {Localisation::getTranslation('faq_73')} 
            {Localisation::getTranslation('faq_74')} 
        </p>
        
    </div>
</div>
        
<div style="margin-top: 25px;text-align: justify" class="well">

    <div id="faqQuestions">

        <h2>{Localisation::getTranslation('faq_terms_list')}</h2>
        <hr />

        <p><a href="#terms1">{Localisation::getTranslation('faq_91')}</a></p>
        <p><a href="#terms2">{Localisation::getTranslation('faq_92')}</a></p>
        <p><a href="#terms3">{Localisation::getTranslation('faq_93')}</a></p>
        <p><a href="#terms4">{Localisation::getTranslation('faq_94')}</a></p>
        <p><a href="#terms5">{Localisation::getTranslation('faq_95')}</a></p>
        <p><a href="#terms6">{Localisation::getTranslation('faq_96')}</a></p>

        <hr />
    </div>

    <div id="faqAnswers">
        <h3 id="terms1">{Localisation::getTranslation('faq_task')}</h3>
        <p>
        {Localisation::getTranslation('faq_78')} 
        {Localisation::getTranslation('faq_79')}
        </p>

        <h3 id="terms2">{Localisation::getTranslation('faq_task_stream')}</h3>
        <p>
        {Localisation::getTranslation('faq_80')} 
        {Localisation::getTranslation('faq_81')} 
        </p>

        <h3 id="terms3">{Localisation::getTranslation('faq_segmentation')}</h3>
        <p>
        {Localisation::getTranslation('faq_82')} 
        {Localisation::getTranslation('faq_83')}
        {Localisation::getTranslation('faq_84')}
        {Localisation::getTranslation('faq_85')}
        {Localisation::getTranslation('faq_86')}
        </p>

        <h3 id="terms4">{Localisation::getTranslation('faq_Tags')}</h3>
        <p>
        {Localisation::getTranslation('faq_87')} 
        </p>

        <h3 id="terms5">{Localisation::getTranslation('faq_badges')}</h3>
        <p>
        {Localisation::getTranslation('faq_88')}
        {Localisation::getTranslation('faq_89')}
        </p>

        <h3 id="terms6">{Localisation::getTranslation('faq_project_impact')}</h3>
        <p>
        {Localisation::getTranslation('faq_90')}
        </p>

    </div>
</div>
            
{include file="footer.tpl"}
