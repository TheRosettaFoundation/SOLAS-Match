{include file="header.tpl"}

{if ($htmlFileExist)}
    {include file="$includeFile"}
{else}
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
            <p><a href="#vol12">{Localisation::getTranslation('faq_question_more_about_trf')}</a></p>
            <p><a href="#vol13">{Localisation::getTranslation('faq_question_uploaded_tasks')}</a></p>
            <p><a href="#vol14">{Localisation::getTranslation('faq_question_work_be_credited')}</a></p>
            <hr />
        </div>

        <div id="volunteerAnswers">
            <a id="vol1" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_v_q1')}</h3>
            <p>{Localisation::getTranslation('faq_v_a1')}</p>

            <a id="vol2" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_v_q2')}</h3>
            <p>{sprintf(Localisation::getTranslation('faq_v_a2'), {Settings::get("site.system_email_address")}, {Settings::get("site.name")})}</p>

            <a id="vol3" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_v_q3')}</h3>
            <p>{sprintf(Localisation::getTranslation('faq_v_a3'), {Settings::get("site.system_email_address")})}</p>

            <a id="vol4" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_v_q4')}</h3>
            <p>{Localisation::getTranslation('faq_v_a4')}</p>

            <a id="vol5" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_v_q5')}</h3>
            <p>{sprintf(Localisation::getTranslation('faq_v_a5'), {Settings::get("site.system_email_address")})}</p>

            <a id="vol6" class="faq-anchor"></a>
            <h3>{sprintf(Localisation::getTranslation('faq_v_q6'), {Settings::get("site.name")})}</h3>
            <p>{sprintf(Localisation::getTranslation('faq_v_a6'), {Settings::get("site.name")})}</p>

            <a id="vol7" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_v_q7')}</h3>
            <p>{Localisation::getTranslation('faq_v_a7')}</p>

            <ol>
                <li>{sprintf(Localisation::getTranslation('faq_v_a7_all'), {Settings::get("site.name")})}</li>
                <li>{Localisation::getTranslation('faq_v_a7_strict')}</li>
            </ol>

            <p>{Localisation::getTranslation('faq_v_a7_2')}</p>

            <a id="vol8" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_27')}</h3>
            <p>
                {Localisation::getTranslation('faq_28')}
                {Localisation::getTranslation('faq_29')}
                {Localisation::getTranslation('faq_30')}
            </p>

            <a id="vol9" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_31')}</h3>
            <p>
               {Localisation::getTranslation('faq_32')}
            </p>

            <a id="vol10" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_33')}</h3>
            <p>
               {Localisation::getTranslation('faq_34')}
               {Localisation::getTranslation('faq_35')}
               {Localisation::getTranslation('faq_36')}
            </p>

            <a id="vol11" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_75')}</h3>
            <p>
                {Localisation::getTranslation('faq_76')}
                {Localisation::getTranslation('faq_77')}
            </p>

            <a id="vol12" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_question_more_about_trf')}</h3>
            <p>
            	{sprintf(Localisation::getTranslation('faq_answer_more_about_trf'), "<a href=\"https://www.therosettafoundation.org/\" target=\"_blank\">www.therosettafoundation.org</a>")}
            </p>

            <a id="vol13" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_question_uploaded_tasks')}</h3>
            <p>
                {Localisation::getTranslation('faq_answer_uploaded_tasks_1')}
                {Localisation::getTranslation('faq_answer_uploaded_tasks_2')}
            </p>

            <a id="vol14" class="faq-anchor"></a>
            <h3>{Localisation::getTranslation('faq_question_work_be_credited')}</h3>
            <p>
                {Localisation::getTranslation('faq_answer_work_be_credited_1')}
                {Localisation::getTranslation('faq_answer_work_be_credited_2')}
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
        <a id="org1" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_o_q1')}</h3>
        <p>{Localisation::getTranslation('faq_o_a1')}</p>

        <a id="org2" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_o_q2')}</h3>
        <p>{Localisation::getTranslation('faq_o_a2')}</p>

        <a id="org3" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_o_q3')}</h3>
        <p>{Localisation::getTranslation('faq_o_a3')}</p>

        <a id="org4" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_o_q4')}</h3>
        <p>{sprintf(Localisation::getTranslation('faq_o_a4'), {Settings::get("site.name")})}</p>

        <a id="org5" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_o_q5')}</h3>
        <p>{Localisation::getTranslation('faq_o_a5')}</p>

        <a id="org6" class="faq-anchor"></a>
    	<h3>{Localisation::getTranslation('faq_59')}</h3>
        <p>
            {Localisation::getTranslation('faq_60')} 
            {Localisation::getTranslation('faq_61')} 
            {Localisation::getTranslation('faq_62')} 
            {Localisation::getTranslation('faq_63')} 
            {Localisation::getTranslation('faq_64')}
        </p>

        <a id="org7" class="faq-anchor"></a>
    	<h3>{Localisation::getTranslation('faq_65')}</h3>
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
        <a id="terms1" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_task')}</h3>
        <p>
        {Localisation::getTranslation('faq_78')} 
        {Localisation::getTranslation('faq_79')}
        </p>

        <a id="terms2" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_task_stream')}</h3>
        <p>
        {Localisation::getTranslation('faq_80')} 
        {Localisation::getTranslation('faq_81')} 
        </p>

        <a id="terms3" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_segmentation')}</h3>
        <p>
            {Localisation::getTranslation('faq_82')}
            {Localisation::getTranslation('faq_83')}
            {Localisation::getTranslation('faq_84')}
            {Localisation::getTranslation('faq_85')}
            {Localisation::getTranslation('faq_86')}
        </p>

        <a id="terms4" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_Tags')}</h3>
        <p>
            {Localisation::getTranslation('faq_87')}
        </p>

        <a id="terms5" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_badges')}</h3>
        <p>
            {Localisation::getTranslation('faq_88')}
            {Localisation::getTranslation('faq_89')}
        </p>

        <a id="terms6" class="faq-anchor"></a>
        <h3>{Localisation::getTranslation('faq_project_impact')}</h3>
        <p>
            {Localisation::getTranslation('faq_90')}
        </p>

    </div>
</div>
{/if}

{include file="footer.tpl"}
