<?php

//use Monolog\Logger;
//use Monolog\Handler\StreamHandler;

class FormController
{
    /**
     * Registration page with form
     */
    public function index()
    {
        $view = new View();
        $view->render('form', []);
    }

    /**
     * Form submit
     */
    public function submit()
    {
        $user = "Tena";

        //get $_POST data, validate data
        $data = $this->_validate($_POST);

        if($data === false) {
            header('Location: ' . App::config('url').'form/index');
        }

        // write to database
        $connection = App::connect();

        $sql = 'INSERT INTO `time_tracker`
                (`user`, `amount`, `description`, `work_type`, `date`)
                VALUES (:name, :user, :amount, :description, :work_type, :date)';

        $stmt = $connection->prepare($sql);
        array_unshift($data, $user);
        $stmt->execute($data);


        $sql = 'SELECT * FROM `time_tracker` ORDER BY `id`';

        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        print_r($result);

        // redirect to thank you page
        //header('Location: ' . App::config('url').'form/thankyou');
    }

    /**
     * @param $data
     * @return array|bool
     */
    private function _validate($data)
    {
        $required = ['amount', 'description', 'work_type', 'date'];

        // remove unknown keys from data if any
        $data = array_diff_key($data, $required);

        //validate required keys
        foreach($required as $key) {
            if(!isset($data[$key])) {
                return false;
            }

            // trim (strip whitespaces from values) then check if empty
            $data[$key] = trim((string)$data[$key]);
            if(empty($data[$key])) {
                return false;
            }
        }

        return $data;
    }

    /**
     * Thank you page
     */
    public function thankyou()
    {
        $view = new View();
        $view->render('thankyou');



        // log new entry

        // create a log channel
//        $log = new Logger('default');
//        $log->pushHandler(new StreamHandler(BP . 'private/default.log', Logger::INFO));
//
//        // add record to the log
//        $log->info('Thank you page, entry was created');
    }

}