<?php

namespace App\Http\Controllers;

use App\Utility\Event;
use Illuminate\Http\Request;

class IndexController extends AbstractController
{
    // Set $controllerName to 'Default' so $this->view->render(YOUR_TEMPLATE_NAME) searches
    // for the html templates in \resources\views\Template\ and not in a controller subdirectory
    protected $controllerName = 'Default';

    /**
     * Index action
     *
     * @return Response
     */
    public function indexAction()
    {
        // Get events
        $this->view->assign('events', Event::serialize($this->calendarRepository->findAll()), Event::FORMAT_JS);

        // Contact form submitted?
        if ($this->request->has('form')) {
            $this->view->assign('form',$this->sendContactEmail(
                $this->request->input('form'),
                [
                    $this->request->input('name'),
                    $this->request->input('email'),
                    $this->request->input('message'),
                ]
            ));
        }

        // We could just do "return $this->view->render .." as well,
        // but by returning a full response object, we can modify headers etc.
        return response($this->view->render('index'));
    }

    /**
     * Send a contact email
     *
     * @param array $form The form data
     * @param array $honeypots
     */
    protected function sendContactEmail($form, $honeypots = [])
    {

        $errors = [
            'fields' => [],
            'general' => []
        ];

        /**
         * Spam protection
         */
        if(self::checkBannedUser()){
            return false;
        }

        if($this->checkHoneypots($honeypots)){
            $errors['general'][] = 'Es tut uns leid, aber du wurdest wegen SPAM-Verdachts gesperrt und kannst keine Formulare mehr abschicken. Kontaktiere uns bitte direkt, danke!';
            $this->bannUser();
        }

        if(isset($_SESSION['lastFormSubmit'])){
            $secondsSinceLastSubmit = time() - $_SESSION['lastFormSubmit'];
            if($secondsSinceLastSubmit <= env('FORM_SPAM_MIN_LASTSUBMIT')){
                $errors['general'][] = 'Bitte warte noch ein wenig, bis du das Formular ein weiteres Mal benutzt.';
                $_SESSION['lastFormSubmit'] = time();
                $this->addUserStrike();
            }
        }

        /**
         * Validation
         */
        if (!$form['name']) {
            $errors['fields']['name'] = 'Pflichtfeld';
        }

        if (!$form['email'] || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['fields']['email'] = 'Keine gültige E-Mail-Adresse';
        }

        if (!$form['message']) {
            $errors['fields']['message'] = 'Pflichtfeld';
        }

        /**
         * Send mail
         */
        $mailsSent = 0;
        if(!count($errors['fields']) && !count($errors['general'])){

            // prepare and send email
            $mailer = new \Swift_Mailer(new \Swift_SendmailTransport(env('MAIL_SENDMAIL_PATH')));
            $message = new \Swift_Message('Danke für deine Anfrage!');
            $message
                ->setFrom([env('FORM_FROM_EMAIL') => env('FORM_FROM_NAME')])
                ->setTo([env('FORM_TO_EMAIL') => env('FORM_TO_NAME')])
                ->setBody(
                    $this->renderStandaloneView('EmailAdmin','Contact', ['form' => $form]),
                    'text/html'
                );

            $mailsSent = $mailer->send($message);
            if(!$mailsSent){
                $errors['general'][] = 'Es gab leider einen Fehler beim Abschicken des Formulars. Bitte versuche es später noch einmal oder kontaktiere uns direkt.';
            }

            $_SESSION['lastFormSubmit'] = time();
        }

        return [
            'data' => $form,
            'errors' => $errors,
            'success' => (!count($errors['fields']) && !count($errors['general']))
        ];
    }

    /**
     * Check if SPAM honeypot fields were filled
     *
     * @param $honeypots
     * @return bool
     */
    protected function checkHoneypots($honeypots){
        foreach($honeypots as $honeypot){
            if($honeypot){
                return true;
            }
        }
        return false;
    }

    /**
     * Check if this user is banned
     * @return bool
     */
    public static function checkBannedUser(){

        $path = env('FORM_BANNED_USERS_FOLDER') . DIRECTORY_SEPARATOR . $_SERVER['REMOTE_ADDR'];
        return count(glob($path)) ? true : false;
    }

    /**
     * Add a strike for this user.
     * Users with too many strikes get banned permanently.
     */
    protected function addUserStrike(){
        if(!isset($_SESSION['strike'])){
            $_SESSION['strike'] = 1;
        }else{
            $_SESSION['strike'] += 1;
        }

        if($_SESSION['strike'] >= env('FORM_STRIKES_FOR_USER_BANN')){
            unset($_SESSION['strike']);
            $this->bannUser();
        }
    }

    /**
     * Banns a user permanently by IP address
     */
    protected function bannUser(){
        if(!file_exists(env('FORM_BANNED_USERS_FOLDER'))){
            mkdir(env('FORM_BANNED_USERS_FOLDER'),0775,true);
        }
        touch(env('FORM_BANNED_USERS_FOLDER') . DIRECTORY_SEPARATOR . $_SERVER['REMOTE_ADDR']);
    }
}