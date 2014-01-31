{include file="header.tpl"}

<div>    
    <div id="title" style="margin-top: 20px; text-align: center">
        <h1>{Localisation::getTranslation(Strings::FAQ_FAQ)}</h1>
    </div>
    
    <div id="quicklinks" style="margin-top: 20px;text-align: center"   >
        <h2>{Localisation::getTranslation(Strings::FAQ_QUICKLINKS)}</h2>
        <h3>
            <a href="#volunteerQuestions">
                {Localisation::getTranslation(Strings::FAQ_VOLUNTEERS)}
            </a>
            <span style="white-space: pre">      |    </span>
            <a href="#orgQuestions">
                {Localisation::getTranslation(Strings::COMMON_ORGANISATION)}
            </a>
            <span style="white-space: pre">      |    </span>
            <a href="#faqQuestions">
                {Localisation::getTranslation(Strings::FAQ_TERMS)}
            </a>
        </h3>
    </div>
    
    <div style="margin-top: 25px;text-align: justify" class="well">
    
        <div id="volunteerQuestions">

            <h2>{Localisation::getTranslation(Strings::FAQ_VOLUNTEER_QUESTIONS)}</h2>
            <hr />

            <p><a href="#vol1">{Localisation::getTranslation(Strings::FAQ_V_Q1)}</a></p>
            <p><a href="#vol2">{Localisation::getTranslation(Strings::FAQ_V_Q2)}</a></p>
            <p><a href="#vol3">{Localisation::getTranslation(Strings::FAQ_V_Q3)}</a></p>
            <p><a href="#vol4">{Localisation::getTranslation(Strings::FAQ_V_Q4)}</a></p>
            <p><a href="#vol5">{Localisation::getTranslation(Strings::FAQ_V_Q5)}</a></p>
            <p><a href="#vol6">{sprintf(Localisation::getTranslation(Strings::FAQ_V_Q6), {Settings::get("site.name")})}</a></p>
            <p><a href="#vol7">{Localisation::getTranslation(Strings::FAQ_V_Q7)}</a></p>   
            <p><a href="#vol8">{Localisation::getTranslation(Strings::FAQ_27)}</a></p> 
            <p><a href="#vol9">{Localisation::getTranslation(Strings::FAQ_31)}</a></p> 
            <p><a href="#vol10">{Localisation::getTranslation(Strings::FAQ_33)}</a></p>
            <p><a href="#vol11">{Localisation::getTranslation(Strings::FAQ_75)}</a></p>
            
            <hr />
        </div>

        <div id="volunteerAnswers">
            <h3 id="vol1">{Localisation::getTranslation(Strings::FAQ_V_Q1)}</h3>
            <p>{Localisation::getTranslation(Strings::FAQ_V_A1)}</p>

            <h3 id="vol2">{Localisation::getTranslation(Strings::FAQ_V_Q2)}</h3>
            <p>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A2), {Settings::get("site.system_email_address")}, {Settings::get("site.name")})}</p>

            <h3 id="vol3">{Localisation::getTranslation(Strings::FAQ_V_Q3)}</h3>
            <p>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A3), {Settings::get("site.system_email_address")})}</p>

            <h3 id="vol4">{Localisation::getTranslation(Strings::FAQ_V_Q4)}</h3>
            <p>{Localisation::getTranslation(Strings::FAQ_V_A4)}</p>

            <h3 id="vol5">{Localisation::getTranslation(Strings::FAQ_V_Q5)}</h3>
            <p>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A5), {Settings::get("site.system_email_address")})}</p>

            <h3 id="vol6">{sprintf(Localisation::getTranslation(Strings::FAQ_V_Q6), {Settings::get("site.name")})}</h3>
            <p>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A6), {Settings::get("site.name")})}</p>

            <h3 id="vol7">{Localisation::getTranslation(Strings::FAQ_V_Q7)}</h3>
            <p>{Localisation::getTranslation(Strings::FAQ_V_A7)}</p>

            <ol>
                <li>{sprintf(Localisation::getTranslation(Strings::FAQ_V_A7_ALL), {Settings::get("site.name")})}</li>
                <li>{Localisation::getTranslation(Strings::FAQ_V_A7_STRICT)}</li>
            </ol>

            <p>{Localisation::getTranslation(Strings::FAQ_V_A7_2)}</p>

            <h3 id="vol8">{Localisation::getTranslation(Strings::FAQ_27)}</h3>
            <p>
            {Localisation::getTranslation(Strings::FAQ_28)} 
            {Localisation::getTranslation(Strings::FAQ_29)} 
            {Localisation::getTranslation(Strings::FAQ_30)}
            </p>

            <h3 id="vol9">{Localisation::getTranslation(Strings::FAQ_31)}</h3>
            <p>
            {Localisation::getTranslation(Strings::FAQ_32)}
            </p>

            <h3 id="vol10">{Localisation::getTranslation(Strings::FAQ_33)}</h3>
            <p>
            {Localisation::getTranslation(Strings::FAQ_34)} 
            {Localisation::getTranslation(Strings::FAQ_35)} 
            {Localisation::getTranslation(Strings::FAQ_36)}
            </p>
            
            <h3 id="vol11">{Localisation::getTranslation(Strings::FAQ_75)}</h3>
            <p>
            {Localisation::getTranslation(Strings::FAQ_76)} 
            {Localisation::getTranslation(Strings::FAQ_77)} 
            </p>

        </div>
    </div>   
            
            
 <div style="margin-top: 20px;text-align: justify" class="well">
    <div id="orgQuestions">
        <h2>{Localisation::getTranslation(Strings::FAQ_ORGANISATION_QUESTIONS)}</h2>
        <hr />
        <p><a href="#org1">{Localisation::getTranslation(Strings::FAQ_O_Q1)}</a></p>
        <p><a href="#org2">{Localisation::getTranslation(Strings::FAQ_O_Q2)}</a></p>
        <p><a href="#org3">{Localisation::getTranslation(Strings::FAQ_O_Q3)}</a></p>
        <p><a href="#org4">{Localisation::getTranslation(Strings::FAQ_O_Q4)}</a></p>
        <p><a href="#org5">{Localisation::getTranslation(Strings::FAQ_O_Q5)}</a></p>
        <p><a href="#org6">{Localisation::getTranslation(Strings::FAQ_59)}</a></p>
        <p><a href="#org7">{Localisation::getTranslation(Strings::FAQ_65)}</a></p>
        <hr />
    </div>
    
    <div id="orgAnswers">
        <h3 id="org1">{Localisation::getTranslation(Strings::FAQ_O_Q1)}</h3>
        <p>{Localisation::getTranslation(Strings::FAQ_O_A1)}</p>
        
        <h3 id="org2">{Localisation::getTranslation(Strings::FAQ_O_Q2)}</h3>
        <p>{Localisation::getTranslation(Strings::FAQ_O_A2)}</p>
        
        <h3 id="org3">{Localisation::getTranslation(Strings::FAQ_O_Q3)}</h3>
        <p>{Localisation::getTranslation(Strings::FAQ_O_A3)}</p>
        
        <h3 id="org4">{Localisation::getTranslation(Strings::FAQ_O_Q4)}</h3>
        <p>{sprintf(Localisation::getTranslation(Strings::FAQ_O_A4), {Settings::get("site.name")})}</p>
        
        <h3 id="org5">{Localisation::getTranslation(Strings::FAQ_O_Q5)}</h3>
        <p>{Localisation::getTranslation(Strings::FAQ_O_A5)}</p>
	
    	<h3 id="org6">{Localisation::getTranslation(Strings::FAQ_59)}</h3>
        <p>
            {Localisation::getTranslation(Strings::FAQ_60)} 
            {Localisation::getTranslation(Strings::FAQ_61)} 
            {Localisation::getTranslation(Strings::FAQ_62)} 
            {Localisation::getTranslation(Strings::FAQ_63)} 
            {Localisation::getTranslation(Strings::FAQ_64)}
        </p>
      
    	<h3 id="org7">{Localisation::getTranslation(Strings::FAQ_65)}</h3>
        <p>
            {Localisation::getTranslation(Strings::FAQ_66)} 
            {Localisation::getTranslation(Strings::FAQ_67)} 
            {Localisation::getTranslation(Strings::FAQ_68)} 
            {Localisation::getTranslation(Strings::FAQ_69)} 
            {Localisation::getTranslation(Strings::FAQ_70)} 
            {Localisation::getTranslation(Strings::FAQ_71)} 
            {Localisation::getTranslation(Strings::FAQ_72)} 
            {Localisation::getTranslation(Strings::FAQ_73)} 
            {Localisation::getTranslation(Strings::FAQ_74)} 
        </p>
        
    </div>
</div>
        
<div style="margin-top: 25px;text-align: justify" class="well">

    <div id="faqQuestions">

        <h2>{Localisation::getTranslation(Strings::FAQ_TERMS_LIST)}</h2>
        <hr />

        <p><a href="#terms1">{Localisation::getTranslation(Strings::FAQ_91)}</a></p>
        <p><a href="#terms2">{Localisation::getTranslation(Strings::FAQ_92)}</a></p>
        <p><a href="#terms3">{Localisation::getTranslation(Strings::FAQ_93)}</a></p>
        <p><a href="#terms4">{Localisation::getTranslation(Strings::FAQ_94)}</a></p>
        <p><a href="#terms5">{Localisation::getTranslation(Strings::FAQ_95)}</a></p>
        <p><a href="#terms6">{Localisation::getTranslation(Strings::FAQ_96)}</a></p>

        <hr />
    </div>

    <div id="faqAnswers">
        <h3 id="terms1">{Localisation::getTranslation(Strings::FAQ_TASK)}</h3>
        <p>
        {Localisation::getTranslation(Strings::FAQ_78)} 
        {Localisation::getTranslation(Strings::FAQ_79)}
        </p>

        <h3 id="terms2">{Localisation::getTranslation(Strings::FAQ_TASK_STREAM)}</h3>
        <p>
        {Localisation::getTranslation(Strings::FAQ_80)} 
        {Localisation::getTranslation(Strings::FAQ_81)} 
        </p>

        <h3 id="terms3">{Localisation::getTranslation(Strings::FAQ_SEGMENTATION)}</h3>
        <p>
        {Localisation::getTranslation(Strings::FAQ_82)} 
        {Localisation::getTranslation(Strings::FAQ_83)}
        {Localisation::getTranslation(Strings::FAQ_84)}
        {Localisation::getTranslation(Strings::FAQ_85)}
        {Localisation::getTranslation(Strings::FAQ_86)}
        </p>

        <h3 id="terms4">{Localisation::getTranslation(Strings::FAQ_TAGS)}</h3>
        <p>
        {Localisation::getTranslation(Strings::FAQ_87)} 
        </p>

        <h3 id="terms5">{Localisation::getTranslation(Strings::FAQ_BADGES)}</h3>
        <p>
        {Localisation::getTranslation(Strings::FAQ_88)}
        {Localisation::getTranslation(Strings::FAQ_89)}
        </p>

        <h3 id="terms6">{Localisation::getTranslation(Strings::FAQ_PROJECT_IMPACT)}</h3>
        <p>
        {Localisation::getTranslation(Strings::FAQ_90)}
        </p>

    </div>
</div>
            
{include file="footer.tpl"}
