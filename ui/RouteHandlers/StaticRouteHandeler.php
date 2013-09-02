<?php
//require_once __DIR__.'/../../Common/lib/Email.php';
class StaticRouteHandeler
{
    public function init()
    {
        $app = Slim::getInstance();       
        $app->get("/static/privacy/", array($this, "privacy"))->name("privacy");
        $app->get("/static/terms/", array($this, "terms"))->name("terms");
        $app->get("/static/faq/", array($this, "faq"))->name("faq");
        $app->get("/static/videos/", array($this, "videos"))->name("videos");
        $app->get("/static/siteLanguage/", array($this, "siteLanguage"))->via("POST","GET")->name("siteLanguage");
        $app->get("/static/getStrings/", array($this, "getStrings"))->name("staticGetStrings");
        $app->get("/static/getUser/", array($this, "getUser"))->name("staticGetUser");
        $app->get("/static/getUserHash/", array($this, "getUserHash"))->name("staticGetUserHash");
        $app->notFound("Middleware::notFound");
//        $app->get("/static/notifyUsers/", array($this, "SendRegistrationEmail"))->name("SendRegistrationEmail");

    }

    public function privacy()
    {
         $app = Slim::getInstance();
         $app->render("static/privacy.tpl");
    }
    
    public function terms()
    {
         $app = Slim::getInstance();
         $app->render("static/terms.tpl");
    }
    
    public function faq()
    {
         $app = Slim::getInstance();
         $app->view()->appendData(array('current_page' => 'faq'));
         $app->render("static/FAQ.tpl");
    }
    
    public function videos()
    {
         $app = Slim::getInstance();
         $app->view()->setData("current_page", "videos");
         $app->render("static/videos.tpl");
    }
    
    public function siteLanguage()
    {
        $app = Slim::getInstance();           
        if($post = $app->request()->post()) {
            if(isset($post['language'])) {
                UserSession::setUserLanguage($post['language']);
            }
            $app->redirect($app->request()->getReferrer());
        }else{
            $app->response()->body(UserSession::getUserLanguage());
        }
    }
    
    public function getUser(){
        if(!is_null(UserSession::getCurrentUserID())){
            $dao = new UserDao();
            Slim::getInstance()->response()->body($dao->getUserDart(UserSession::getCurrentUserID()));           
        }
    }
    
    public function getUserHash(){
        if(!is_null(UserSession::getAccessToken())){
               Slim::getInstance()->response()->body(UserSession::getAccessToken()->getToken());           
        }
    }
    
    public function getStrings(){
        Slim::getInstance()->response()->body(Localisation::getStrings());
    }
    
//    public function SendRegistrationEmail(){
//        $userDao = new UserDao();
//        $data=
//"3803,421bd850bce4f4a01f52f04090d18500
//3804,49b70cb2c42d9164ddbfd66f2b1b2d7a
//3805,94fce3056ee78aa92a6b2affe612e9f1
//3806,86b3e973df58dfc48ffb3d101e15d49e
//3808,68c031c626d594599f4a127219c6ae48
//3809,2241522de4999b800972db3c093d3111
//3810,edf235025a3ac2b85d89d41d93e34776
//3811,85c20dd2b382abf17260f55229db1d14
//3812,771d2a1fd3b4744e22cf794911a0315f
//3813,bace096e01ab6dbbec410b4b6cdc0219
//3814,a38a26017a498bd6a77820643e595983
//3815,bb43a915a9c386cc2ea4cec48b9fed96
//3816,1ed53a8b4f08324e6afae1be396b301a
//3817,2fb1cf98663e7627c3025c4fd7d010da
//3819,6843273b7ac65738144828be6a81d473
//3820,8880b538b4edd9612d0078966cb2cef1
//3822,935bb8ca803779cd6ef8ea2a51a74f23
//3827,8f47493a935ef37e46358b13a85851cb
//3836,87503817e489f923d3574d50e5518690
//3837,0c30a98071df4c0a2ee5921475b78593
//3839,b72a079d170d851025b38c7bea49274b
//3840,7e56fa007cd1f782cef385b791cde816
//3842,92b192cc03ba368be7c0c39db8c9a530
//3843,ef8d54f9741ebdc025824b6cc157285c
//3844,4fbf9df77924fe3188b1323f0ac1504b
//3848,d368d478b1a261fd35fa4530186a148e
//3849,569031e12d08c8c3efef3df7b0b7e557
//3850,f96eeed0f7dfcecfaf93736d25371cc9
//3851,8b62451d3b3cc7ef27fe932f53e24e41
//3852,364231f1a8fc05bed0f92789a97825db
//3853,c542c8f1fa5dee005fe6b3d32fefb6e2
//3854,84d3afbe649554c02708e22d9f816bf7
//3855,4e017fa43145a21aa16306566995fc36
//3857,ad40aa092ad17954f71d517281dff1a4
//3858,e546c43b20f83bd8a417d459c261dab4
//3859,a086a7a475af776a498ecfac0e9783d7
//3863,3c70f2eac2092f44c56f7e0cef50e4b0
//3865,a226e625e414ea2a18c335ef4284ba6c
//3866,bf1162f36557a6575036f52e32bdf294
//3867,60fa5ebaa5b833532655db2eac5f3ce1
//3868,50a540149662f89584c06f6cbdc26aba";
//        $data=explode("\n", $data);
//        foreach($data as $value){
//            $temp = explode(",", $value);
//            $userID=$temp[0];
//            $uid=$temp[1];
//       
//        $user = $userDao->getUser($userID);
//        $message="
//            
//        Hi,
//        
//        Thank you for registering on trommons.org
//        
//        We have recently experienced problems with our registration syetem that
//        blocked users from completing registration.
//        
//        We believe that your account may have been affected by this error.
//        
//        If you have not already done so, you can verify your account by clicking on the
//        following link:   %s                 .
//        
//        Once this has been done, your account will be active and you will be able to login and access all features
//        of the site.
//        
//        We would like to apologise for any inconveniece this has caused.
//        
//        If you continue to experence any login issues, please contact us at trommons@therosettafoundation.org or info@trommons.org
//        
//        Regards,
//        The Trommons Team.
//
//
//
//";
//        $link= "http://trommons.org/user/$uid/verification/?";
//        //echo $user->getEmail();
//        
//        Email::sendEmail($user->getEmail(), "Welcome To Trommons.org", sprintf($message,$link));
//        }
//    }
}

$route_handler = new StaticRouteHandeler();
$route_handler->init();
unset ($route_handler);
