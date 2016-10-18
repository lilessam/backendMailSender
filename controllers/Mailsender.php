<?php namespace Lilessam\Mailsender\Controllers;
use DB;
use Flash;
use Redirect;
use BackendMenu;
use Backend\Classes\Controller;
use Backend\Models\User;
use ApplicationException;
use Mail;
use Lang;
use Input;
use Storage;
use Request;

 /*****************************************************************************
 ******************************************************************************
 ************************************ Mail Sender *****************************
 *                                                                            *
 *                              Developed By Lil'Essam                        *
 *                                                                            *
 *****************************************************************************/

class Mailsender extends Controller
{
    /**
     * Implementing Behaviors
     * */
    public $implement = [
        'Backend.Behaviors.ListController',
    ];

    /**
     * Setting Configurations
     * */
    public $listConfig = 'config_list.yaml';

    /**
     * __Cunstructing
     * */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Lilessam.Mailsender', 'mailsender', 'mailsender');
    }

    /**
     * Passing to form
     * */
    public function index()
    {
        $this->vars['groups'] = DB::table('backend_user_groups')->get();
        $this->asExtension('ListController')->index();

    }
    /**
    * Getting group users
    **/
    public function onGetUsers()
    {
        $group_id_row = DB::table('backend_user_groups')->where('id', post('group_id'))->first();
        $users_ids = DB::table('backend_users_groups')->where('user_group_id', $group_id_row->id)->get();
        $users = [];
        foreach($users_ids as $user_id):
            $user = \Backend\Models\User::find($user_id->user_id);
            array_push($users, $user);
        endforeach;
        return ['users' => $users];
    }

    /**
     * Sending Mail
     * */
    public function onSendMail()
    {
        //print_r(post());
        /**
         * Getting form results
         * */
        $group_id = post('group');
        $subject = post('subject');
        $msg = post('message');
        $test_email = post('testEmail');
        $users = post('user');
        if(Input::file('attachment') != null):
            $file = new \System\Models\File;
        	$file->data = Input::file('attachment');
        	$file->save();
        endif;
       
        /**
         * Checking if there's no data
         * */
        if($subject == "" || $msg == ""):
            return Flash::warning(Lang::get('lilessam.mailsender::lang.error_nodata'));
        endif;
        
        /**
         * Striping tags for the plain version of mail
         * */
        $msgPlain = strip_tags(post('message'));
        if(Input::file('attachment') != null):
            $attachment = $file->getLocalPath();
        else:
            $attachment = null;
        endif;
        /**
         * Setting vars array for mail template
         * */
        $vars = [
            'subject' => $subject,
            'msg' => $msg,
            'msgPlain' => $msgPlain,
            ];
            
        /**
         * Check if the administrator want to send only a test message
         * */
        if($test_email != "")
        {
            //email and subject array
            $array = [
            'email' => $test_email,
            'subject'=>$subject,
            'attachment' => $attachment,
            ];
            //Sending mail
            Mail::send([
                        'text' => $msgPlain,
                        'html' => $msg,
                        'raw' => true
                    ], $vars, function($message) use ($array){
            		$message->subject($array['subject']);
            		if(isset($array['attachment'])){
                        $message->attach($array['attachment']);
                    }
		    	    $message->to($array['email'], "Test Reciever");
	    	});
	    	
	    	/**
	    	 * Success message
	    	 * */
	    	Flash::success(Lang::get('lilessam.mailsender::lang.test.sent'));
	    	return Redirect::back();

        }elseif(isset($users)){
            /**
            * Fetch users
            */
            foreach($users as $key => $value)
            {

            $user = \Backend\Models\User::find($key);

            //email and subject array
            $array = [
            'email' => $user->email,
            'subject'=>$subject,
            'attachment' => $attachment,
            ];
            //Sending mail
            Mail::send([
                        'text' => $msgPlain,
                        'html' => $msg,
                        'raw' => true
                    ], $vars, function($message) use ($array){
                    $message->subject($array['subject']);
                    if(isset($array['attachment'])){
                        $message->attach($array['attachment']);
                    }
                    $message->to($array['email'], "Test Reciever");
            });
            
            
            }
            /**
             * Success message
             * */
            Flash::success(Lang::get('lilessam.mailsender::lang.chosen.sent'));
            return Redirect::back();
        }
            
        
        
        /**
         * Getting users count in this group
         * */
        $users_count = DB::table('backend_users_groups')->where('user_group_id', $group_id)->count();

        /**
         * Checking if there's users in the group
         * */
            if($users_count != 0):
                //Fetching users
                $users_ids = DB::table('backend_users_groups')->where('user_group_id', $group_id)->get();

                /**
                 * Looping to send mail to every user
                 * */
                foreach($users_ids as $user_id){
                    //The user
                    $user = User::where('id', $user_id->user_id)->first();
                    //User and subject array
                    $array = [
                    'user' => $user,
                    'subject'=>$subject,
                    'attachment' => $attachment,
                    ];
                    //Sending mail
                    Mail::send([
                                'text' => $msgPlain,
                                'html' => $msg,
                                'raw' => true
                            ], $vars, function($message) use ($array){
                    		$message->subject($array['subject']);
                            if(isset($array['attachment'])){
                    	       $message->attach($array['attachment']);
                            }
        		    	    $message->to($array['user']->email, $array['user']->login);
        	    	});
                }
            /**
             * Success Message
             * */
            Flash::success(Lang::get('lilessam.mailsender::lang.sent').$users_count.Lang::get('lilessam.mailsender::lang.users'));
            return Redirect::back();
        else:
            /**
             * Warning message that there's no users in this group
             * */
            Flash::warning(Lang::get('lilessam.mailsender::lang.nousers'));
            return Redirect::back();
        endif;

    }
    
    /**
     * This function checks if there's a value in test email field.
     * if there's any value the send button to all group will be hidden
     * */
    public function onCheckTestEmail()
    {
        if(post('testEmail') == ""){
		return  ['correct'=> 0];
    	}else{
    		return  ['correct'=> 1];
    	}
    }
}
